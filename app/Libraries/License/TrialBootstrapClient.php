<?php

declare(strict_types=1);

namespace App\Libraries\License;

use App\Libraries\Installer\InstallationIdentity;
use Config\License;
use RuntimeException;

final class TrialBootstrapClient
{
    /*
     * Normalized License Server response states.
     *
     * NEW_TRIAL:
     * Installation UUID has no existing license.
     *
     * EXISTING_TRIAL:
     * Installation UUID already has Trial license.
     *
     * EXISTING_FULL:
     * Installation UUID already has Full license.
     */
    public const RESPONSE_NEW_TRIAL =
        'NEW_TRIAL';

    public const RESPONSE_EXISTING_TRIAL =
        'EXISTING_TRIAL';

    public const RESPONSE_EXISTING_FULL =
        'EXISTING_FULL';

    private License $config;

    private InstallationIdentity $identity;

    public function __construct(
        ?License $config = null,
        ?InstallationIdentity $identity = null
    ) {
        $this->config = $config ?? config('License');

        $this->identity = $identity
            ?? new InstallationIdentity();

        if (
            trim((string) $this->config->baseUrl) === ''
        ) {
            throw new RuntimeException(
                'LICENSE_BASE_URL is required.'
            );
        }

        if (
            trim((string) $this->config->appVersion) === ''
        ) {
            throw new RuntimeException(
                'LICENSE_APP_VERSION is required.'
            );
        }
    }

    /**
     * Request Trial License automatically.
     *
     * Installation UUID is resolved internally
     * from InstallationIdentity.
     *
     * No License Key or API Key is entered
     * manually by the user.
     *
     * License Server determines whether this
     * application installation is eligible
     * for a Trial License.
     */
    /**
     * Read-only Trial installation decision.
     *
     * Does NOT claim or create a Trial License.
     *
     * Returns:
     * - NEW_TRIAL
     * - EXISTING_TRIAL
     * - EXISTING_FULL
     */
    public function requestTrialDecision(): array
    {
        $installationUuid = $this->identity->uuid();

        if ($installationUuid === '') {
            throw new RuntimeException(
                'Installation UUID is required.'
            );
        }

        $client = service(
            'curlrequest',
            [
                'baseURI' =>
                    rtrim(
                        (string) $this->config->baseUrl,
                        '/'
                    ) . '/',

                'timeout' =>
                    $this->config->timeout,
            ]
        );

        $payload = [
            'installation_uuid' =>
                $installationUuid,

            'app_version' =>
                $this->config->appVersion,
        ];

        $timestamp = (string) time();
        $nonce = $this->generateNonce();

        try {
            $response = $client->request(
                'POST',
                'api/v1/trial/check',
                [
                    'headers' => [
                        'Content-Type' =>
                            'application/json',

                        'Accept' =>
                            'application/json',

                        'X-Timestamp' =>
                            $timestamp,

                        'X-Nonce' =>
                            $nonce,

                        'X-App-Version' =>
                            $this->config->appVersion,
                    ],

                    'body' => json_encode(
                        $payload,
                        JSON_UNESCAPED_SLASHES
                        | JSON_UNESCAPED_UNICODE
                        | JSON_THROW_ON_ERROR
                    ),

                    'http_errors' => false,
                ]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'Trial decision request failed: '
                . $e->getMessage(),
                0,
                $e
            );
        }

        $status = $response->getStatusCode();
        $body = (string) $response->getBody();

        $decoded = json_decode(
            $body,
            true
        );

        if (! is_array($decoded)) {
            throw new RuntimeException(
                'Invalid Trial decision response.'
            );
        }

        if (
            $status < 200 ||
            $status >= 300
        ) {
            throw new RuntimeException(
                (string) (
                    $decoded['message']
                    ?? 'Trial decision request rejected.'
                )
            );
        }

        if (
            ($decoded['success'] ?? false)
            !== true
        ) {
            throw new RuntimeException(
                (string) (
                    $decoded['message']
                    ?? 'Trial decision request failed.'
                )
            );
        }

        $data = $decoded['data'] ?? null;

        if (! is_array($data)) {
            throw new RuntimeException(
                'Trial decision response data is invalid.'
            );
        }

        $state = strtoupper(
            trim(
                (string) (
                    $data['install_status']
                    ?? ''
                )
            )
        );

        if (
            ! in_array(
                $state,
                [
                    self::RESPONSE_NEW_TRIAL,
                    self::RESPONSE_EXISTING_TRIAL,
                    self::RESPONSE_EXISTING_FULL,
                ],
                true
            )
        ) {
            throw new RuntimeException(
                'Unknown Trial installation state.'
            );
        }

        return [
            'success' => true,
            'install_status' => $state,
            'data' => $data,
        ];
    }

    public function requestTrial(): array
    {
        $installationUuid = $this->identity->uuid();

        if ($installationUuid === '') {
            throw new RuntimeException(
                'Installation UUID is required.'
            );
        }

        log_message(
            'info',
            'TRIAL_REQUEST_ENTERED'
        );

        log_message(
            'info',
            'TRIAL_REQUEST_INSTALLATION_UUID='
            . $installationUuid
        );

        $client = service(
            'curlrequest',
            [
                'baseURI' =>
                    rtrim(
                        (string) $this->config->baseUrl,
                        '/'
                    ) . '/',

                'timeout' =>
                    $this->config->timeout,
            ]
        );

        $payload = [
            'installation_uuid' =>
                $installationUuid,

            'application_code' =>
                $this->applicationCode(),

            'app_version' =>
                $this->config->appVersion,
        ];

        $timestamp = (string) time();
        $nonce = $this->generateNonce();

        try {

            $response = $client->request(
                'POST',
                'api/v1/trial/claim',
                [
                    'headers' => [
                        'Content-Type' =>
                            'application/json',

                        'Accept' =>
                            'application/json',

                        'X-Timestamp' =>
                            $timestamp,

                        'X-Nonce' =>
                            $nonce,

                        'X-App-Version' =>
                            $this->config->appVersion,
                    ],

                    'body' => json_encode(
                        $payload,
                        JSON_UNESCAPED_SLASHES
                        | JSON_UNESCAPED_UNICODE
                        | JSON_THROW_ON_ERROR
                    ),

                    'http_errors' => false,
                ]
            );

        } catch (\Throwable $e) {

            log_message(
                'error',
                'TRIAL_REQUEST_EXCEPTION='
                . $e->getMessage()
            );

            throw new RuntimeException(
                'Tidak dapat menghubungi License Server.'
            );
        }

        $status = $response->getStatusCode();

        $raw = (string) $response->getBody();

        log_message(
            'info',
            'TRIAL_SERVER_RESPONSE_RECEIVED status='
            . $status
        );

        $data = json_decode(
            $raw,
            true
        );

        if (!is_array($data)) {

            log_message(
                'error',
                'TRIAL_REQUEST_NON_JSON status='
                . $status
            );

            throw new RuntimeException(
                'License Server mengembalikan '
                . "response tidak valid (HTTP {$status})."
            );
        }

        if (
            $status < 200
            ||
            $status >= 300
        ) {

            $message = trim(
                (string) (
                    $data['message']
                    ?? ''
                )
            );

            if ($message === '') {
                $message =
                    "Permintaan Trial ditolak "
                    . "(HTTP {$status}).";
            }

            throw new RuntimeException(
                $message
            );
        }

        if (
            ($data['success'] ?? false)
            !== true
        ) {

            throw new RuntimeException(
                (string) (
                    $data['message']
                    ?? 'Permintaan Trial gagal.'
                )
            );
        }

        $result = $data['data'] ?? null;

        if (!is_array($result)) {

            throw new RuntimeException(
                'Data Trial dari License Server '
                . 'tidak valid.'
            );
        }

        $licenseType = strtolower(
            trim(
                (string) (
                    $result['license_type']
                    ?? ''
                )
            )
        );

        /*
         * Normalize license state returned by
         * License Server.
         *
         * Trial request can produce:
         *
         * - NEW_TRIAL
         * - EXISTING_TRIAL
         * - EXISTING_FULL
         */
        $responseState = trim(
            (string) (
                $result['install_status']
                ?? ''
            )
        );

        $responseState = strtoupper(
            $responseState
        );

        /*
         * Response state MUST be explicitly returned
         * by the License Server.
         *
         * Allowed states:
         *
         * - NEW_TRIAL
         * - EXISTING_TRIAL
         * - EXISTING_FULL
         */
        if ($responseState === '') {

            throw new RuntimeException(
                'License Server tidak mengembalikan '
                . 'response_state instalasi.'
            );
        }

        /*
         * Existing Trial.
         *
         * Return normalized state immediately.
         * Credential is not required because the
         * application must not reinstall Trial.
         */
        if (
            $responseState ===
            self::RESPONSE_EXISTING_TRIAL
        ) {

            return [
                'response_state' =>
                    self::RESPONSE_EXISTING_TRIAL,

                'installation_uuid' =>
                    $installationUuid,

                'license_type' =>
                    'trial',

                'message' =>
                    (string) (
                        $result['message']
                        ?? 'Installation UUID sudah memiliki lisensi Trial.'
                    ),
            ];
        }

        /*
         * Existing Full.
         *
         * Stop Trial installation immediately.
         */
        if (
            $responseState ===
            self::RESPONSE_EXISTING_FULL
        ) {

            return [
                'response_state' =>
                    self::RESPONSE_EXISTING_FULL,

                'installation_uuid' =>
                    $installationUuid,

                'license_type' =>
                    'full',

                'message' =>
                    (string) (
                        $result['message']
                        ?? 'Installation UUID sudah memiliki lisensi Full.'
                    ),
            ];
        }

        /*
         * Only NEW_TRIAL may continue to receive
         * Trial credentials.
         */
        if (
            $responseState !==
            self::RESPONSE_NEW_TRIAL
        ) {

            throw new RuntimeException(
                'License Server mengembalikan '
                . 'status instalasi tidak dikenal.'
            );
        }

        if ($licenseType !== 'trial') {

            throw new RuntimeException(
                'License Server tidak mengembalikan '
                . 'lisensi Trial baru.'
            );
        }

        $licenseKey = strtoupper(
            trim(
                (string) (
                    $result['license_key']
                    ?? ''
                )
            )
        );

        if ($licenseKey === '') {

            throw new RuntimeException(
                'License Server tidak mengembalikan '
                . 'Trial License.'
            );
        }

        $apiKey = trim(
            (string) (
                $result['api_key']
                ?? ''
            )
        );

        log_message(
            'info',
            'TRIAL_RESPONSE_VALIDATED status='
            . ($result['status'] ?? 'unknown')
            . ' license_type='
            . ($result['license_type'] ?? 'unknown')
        );

        if ($apiKey === '') {

            throw new RuntimeException(
                'License Server tidak mengembalikan '
                . 'API Key.'
            );
        }

        $apiSecret = trim(
            (string) (
                $result['api_secret']
                ?? ''
            )
        );

        if ($apiSecret === '') {

            throw new RuntimeException(
                'License Server tidak mengembalikan '
                . 'API Secret.'
            );
        }

        $responseUuid = trim(
            (string) (
                $result['installation_uuid']
                ?? ''
            )
        );

        if (
            $responseUuid !== ''
            &&
            !hash_equals(
                $installationUuid,
                $responseUuid
            )
        ) {

            throw new RuntimeException(
                'Installation UUID response mismatch.'
            );
        }

        log_message(
            'info',
            'TRIAL_REQUEST_SUCCESS'
        );

        return [
            'response_state' =>
                self::RESPONSE_NEW_TRIAL,

            'installation_uuid' =>
                $installationUuid,

            'license_type' =>
                $licenseType,

            'license_key' =>
                $licenseKey,

            'api_key' =>
                $apiKey,

            'api_secret' =>
                $apiSecret,

            'status' =>
                strtolower(
                    trim(
                        (string) (
                            $result['status']
                            ?? 'valid'
                        )
                    )
                ),

            'expires_at' =>
                $result['expires_at']
                ?? null,

            'message' =>
                (string) (
                    $result['message']
                    ?? ''
                ),
        ];
    }

    /**
     * Return existing Installation UUID.
     *
     * This method is useful for diagnostics
     * and installer status checks.
     */
    public function installationUuid(): string
    {
        return $this->identity->uuid();
    }

    /**
     * Generate request nonce.
     */
    private function sortRecursively(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        if (array_is_list($value)) {
            return array_map(
                fn (mixed $item): mixed =>
                    $this->sortRecursively($item),
                $value
            );
        }

        foreach ($value as $key => $item) {
            $value[$key] =
                $this->sortRecursively($item);
        }

        ksort($value);

        return $value;
    }

    private function generateNonce(): string
    {
        return bin2hex(random_bytes(24));
    }

    /**
     * Resolve application code.
     */
    private function applicationCode(): string
    {
        $code = trim(
            (string) env(
                'LICENSE_APP_CODE',
                'PRESENSI'
            )
        );

        return $code !== ''
            ? strtoupper($code)
            : 'PRESENSI';
    }
}
