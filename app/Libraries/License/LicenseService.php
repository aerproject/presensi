<?php

declare(strict_types=1);

namespace App\Libraries\License;

use App\Models\LicenseRuntimeModel;
use RuntimeException;

final class LicenseService
{
    private ?LicenseClient $client = null;
    private LicenseRuntimeModel $runtime;
    private ServerIdentity $identity;
    private LicenseCrypto $crypto;
    private LicenseTypeResolver $typeResolver;

    public function __construct(
        ?LicenseClient $client = null,
        ?LicenseRuntimeModel $runtime = null,
        ?ServerIdentity $identity = null,
        ?LicenseCrypto $crypto = null
    ) {
        $this->client = $client;
        $this->runtime = $runtime ?? new LicenseRuntimeModel();
        $this->identity = $identity ?? new ServerIdentity($this->runtime);
        $this->crypto = $crypto ?? new LicenseCrypto();
        $this->typeResolver = new LicenseTypeResolver();
    }

    private function client(): LicenseClient
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $credentialStore = new LicenseCredentialStore(
            $this->runtime,
            $this->crypto
        );

        $credential = $credentialStore->get();

        if (is_array($credential)) {
            $this->client = new LicenseClient(
                apiKey: $credential['api_key'],
                apiSecret: $credential['api_secret']
            );

            return $this->client;
        }

        throw new RuntimeException(
            'Operational license credential is not available. Bootstrap is required.'
        );
    }

    public function runtime(): array
    {
        return $this->runtime->ensureRuntime();
    }

    public function licenseKey(): ?string
    {
        $row = $this->runtime->ensureRuntime();

        if (
            empty($row['license_key_enc']) ||
            empty($row['license_key_iv']) ||
            empty($row['license_key_tag'])
        ) {
            return null;
        }

        return $this->crypto->decrypt(
            $row['license_key_enc'],
            $row['license_key_iv'],
            $row['license_key_tag']
        );
    }

    public function storeLicense(string $licenseKey): array
    {
        $licenseKey = strtoupper(trim($licenseKey));

        $licenseType = $this->typeResolver->resolve(
            $licenseKey
        );

        if ($licenseKey === '') {
            throw new RuntimeException(
                'license_key is required.'
            );
        }

        $encrypted = $this->crypto->encrypt($licenseKey);

        $this->runtime->ensureRuntime();

        $this->runtime->updateRuntime([
            'license_key_enc' => $encrypted['ciphertext'],
            'license_key_iv'  => $encrypted['iv'],
            'license_key_tag' => $encrypted['tag'],
            'license_type'   => $licenseType,
            'activate_status' => null,
            'validate_status' => null,
            'server_id'       => null,
            'server_hash'     => null,
            'expires_at'      => null,
            'activated_at'    => null,
            'last_validation_at' => null,
            'last_heartbeat_at' => null,
            'app_version'     => config('License')->appVersion,
        ]);

        return [
            'stored' => true,
            'license_masked' => $this->maskLicense($licenseKey),
        ];
    }

    public function activate(?string $licenseKey = null): array
    {
        $licenseKey = trim(
            (string) ($licenseKey ?? $this->licenseKey() ?? '')
        );

        if ($licenseKey === '') {
            throw new RuntimeException(
                'License key is not configured.'
            );
        }

        $this->runtime->ensureRuntime();

        $payload = array_merge(
            [
                'license_key' => $licenseKey,
            ],
            $this->identity->get()
        );

        $response = $this->client()->activate($payload);

        $data = $response['data'] ?? null;

        if (
            !is_array($data)
            || ($response['success'] ?? false) !== true
        ) {
            throw new RuntimeException(
                (string) (
                    $response['message']
                    ?? 'License activation failed.'
                )
            );
        }

        $runtime = $this->runtime->ensureRuntime();

        $metadata = [];

        if (
            !empty($runtime['metadata_json'])
        ) {
            $decoded = json_decode(
                (string) $runtime['metadata_json'],
                true
            );

            if (is_array($decoded)) {
                $metadata = $decoded;
            }
        }

        $metadata['activation'] = $data;

        $this->runtime->updateRuntime([
            'server_id' => $data['server_id'] ?? null,
            'server_hash' => $data['server_hash'] ?? null,
            'license_type' => isset($data['license_type'])
                && trim((string) $data['license_type']) !== ''
                    ? strtolower(trim((string) $data['license_type']))
                    : $this->typeResolver->resolve($licenseKey),
            'activate_status' => $data['status'] ?? 'active',
            'activated_at' => $this->normalizeDate(
                $data['activated_at'] ?? null
            ),
            'expires_at' => $this->normalizeDate(
                $data['expires_at'] ?? null
            ),
            'app_version' => config('License')->appVersion,
            'metadata_json' => json_encode(
                $metadata,
                JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
            ),
        ]);

        return [
            'success' => true,
            'message' => $response['message'] ?? null,
            'data' => $data,
        ];
    }

    public function validate(): array
    {
        $licenseKey = $this->licenseKey();

        if ($licenseKey === null) {
            throw new RuntimeException(
                'License key is not configured.'
            );
        }

        $runtime = $this->runtime->ensureRuntime();

        $serverHash = trim(
            (string) ($runtime['server_hash'] ?? '')
        );

        if ($serverHash === '') {
            throw new RuntimeException(
                'server_hash is not available.'
            );
        }

        $response = $this->client()->validate([
            'license_key' => $licenseKey,
            'server_uuid' => $this->identity->serverUuid(),
            'server_hash' => $serverHash,
            'app_version' => config('License')->appVersion,
        ]);

        $data = $response['data'] ?? null;

        if (
            !is_array($data)
            || ($response['success'] ?? false) !== true
        ) {
            throw new RuntimeException(
                (string) (
                    $response['message']
                    ?? 'License validation failed.'
                )
            );
        }

        $metadata = [];

        if (
            !empty($runtime['metadata_json'])
        ) {
            $decoded = json_decode(
                (string) $runtime['metadata_json'],
                true
            );

            if (is_array($decoded)) {
                $metadata = $decoded;
            }
        }

        $metadata['validation'] = $data;

        $this->runtime->updateRuntime([
            'validate_status' => strtolower(
                trim((string) ($data['status'] ?? ''))
            ) ?: 'unknown',
            'license_type' => isset($data['license_type'])
                && trim((string) $data['license_type']) !== ''
                    ? strtolower(trim((string) $data['license_type']))
                    : $this->typeResolver->resolve($licenseKey),
            'expires_at' => $this->normalizeDate(
                $data['expires_at']
                ?? $runtime['expires_at']
                ?? null
            ),
            'last_validation_at' => date(
                'Y-m-d H:i:s'
            ),
            'metadata_json' => json_encode(
                $metadata,
                JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
            ),
        ]);

        return [
            'success' => true,
            'message' => $response['message'] ?? null,
            'data' => $data,
        ];
    }

    public function heartbeat(): array
    {
        $licenseKey = $this->licenseKey();

        if ($licenseKey === null) {
            throw new RuntimeException(
                'License key is not configured.'
            );
        }

        $runtime = $this->runtime->ensureRuntime();

        $serverHash = trim(
            (string) ($runtime['server_hash'] ?? '')
        );

        if ($serverHash === '') {
            throw new RuntimeException(
                'server_hash is not available.'
            );
        }

        $response = $this->client()->heartbeat(
            array_merge(
                [
                    'license_key' => $licenseKey,
                    'server_hash' => $serverHash,
                ],
                $this->identity->get()
            )
        );

        $data = $response['data'] ?? null;

        if (
            !is_array($data)
            || ($response['success'] ?? false) !== true
        ) {
            throw new RuntimeException(
                (string) (
                    $response['message']
                    ?? 'License heartbeat failed.'
                )
            );
        }

        $metadata = [];

        if (
            !empty($runtime['metadata_json'])
        ) {
            $decoded = json_decode(
                (string) $runtime['metadata_json'],
                true
            );

            if (is_array($decoded)) {
                $metadata = $decoded;
            }
        }

        $metadata['heartbeat'] = $data;

        $this->runtime->updateRuntime([
            'last_heartbeat_at' => date(
                'Y-m-d H:i:s'
            ),
            'expires_at' => $this->normalizeDate(
                $data['expires_at']
                ?? $runtime['expires_at']
                ?? null
            ),
            'metadata_json' => json_encode(
                $metadata,
                JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
            ),
        ]);

        return [
            'success' => true,
            'message' => $response['message'] ?? null,
            'data' => $data,
        ];
    }

    /**
     * Submit a Full license renewal request.
     *
     * This method only submits the request to the License Server.
     * It does not modify the local license runtime.
     *
     * @return array<string, mixed>
     */
    public function submitRenewalRequest(
        string $requestUuid,
        ?string $notes = null
    ): array {
        $licenseKey = $this->licenseKey();

        if ($licenseKey === null) {
            throw new RuntimeException(
                'License key is not configured.'
            );
        }

        $requestUuid = trim($requestUuid);

        if ($requestUuid === '') {
            throw new RuntimeException(
                'request_uuid is required.'
            );
        }

        $runtime = $this->runtime->ensureRuntime();

        $serverHash = trim(
            (string) ($runtime['server_hash'] ?? '')
        );

        if ($serverHash === '') {
            throw new RuntimeException(
                'server_hash is not available.'
            );
        }

        $payload = [
            'request_uuid' => $requestUuid,
            'license_key' => $licenseKey,
            'server_hash' => $serverHash,
            'server_uuid' => $this->identity->serverUuid(),
            'notes' => $notes !== null
                ? trim($notes)
                : null,
        ];

        $response = $this->client()->renewalRequest(
            $payload
        );

        if (
            ($response['success'] ?? false) !== true
        ) {
            throw new RuntimeException(
                (string) (
                    $response['message']
                    ?? 'License renewal request failed.'
                )
            );
        }

        $data = $response['data'] ?? null;

        if (!is_array($data)) {
            throw new RuntimeException(
                'Invalid renewal request response.'
            );
        }

        return $data;
    }

    private function normalizeDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $normalized = str_replace('T', ' ', $value);
        $normalized = preg_replace(
            '/[+-]\d{2}:\d{2}$/',
            '',
            $normalized
        );
        $normalized = preg_replace(
            '/\.\d+$/',
            '',
            $normalized
        );

        return $normalized ?: null;
    }

    private function maskLicense(string $licenseKey): string
    {
        $parts = explode('-', $licenseKey);

        if (count($parts) < 2) {
            return '****';
        }

        return $parts[0]
            . '-'
            . $parts[1]
            . '-****-****-****';
    }
}
