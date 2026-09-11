<?php

declare(strict_types=1);

namespace App\Libraries\License;

use App\Models\LicenseRuntimeModel;
use RuntimeException;

final class LicenseCredentialStore
{
    private const META_KEY = 'license_credential';

    public function __construct(
        private readonly LicenseRuntimeModel $runtime,
        private readonly LicenseCrypto $crypto
    ) {
    }

    /**
     * Store operational License API credential securely.
     *
     * Plaintext credential values are never stored in metadata_json.
     */
    public function store(
        string $apiKey,
        string $apiSecret
    ): bool {
        $apiKey = trim($apiKey);
        $apiSecret = trim($apiSecret);

        if ($apiKey === '') {
            throw new RuntimeException(
                'API key is required.'
            );
        }

        if ($apiSecret === '') {
            throw new RuntimeException(
                'API secret is required.'
            );
        }

        $current = $this->runtime->ensureRuntime();

        $metadata = $this->decodeMetadata(
            $current['metadata_json'] ?? null
        );

        $apiKeyEncrypted = $this->crypto->encrypt(
            $apiKey
        );

        $apiSecretEncrypted = $this->crypto->encrypt(
            $apiSecret
        );

        $metadata[self::META_KEY] = [
            'api_key_enc' => $apiKeyEncrypted['ciphertext'],
            'api_key_iv' => $apiKeyEncrypted['iv'],
            'api_key_tag' => $apiKeyEncrypted['tag'],
            'api_secret_enc' => $apiSecretEncrypted['ciphertext'],
            'api_secret_iv' => $apiSecretEncrypted['iv'],
            'api_secret_tag' => $apiSecretEncrypted['tag'],
        ];

        return $this->runtime->updateRuntime([
            'metadata_json' => json_encode(
                $metadata,
                JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_THROW_ON_ERROR
            ),
        ]);
    }

    /**
     * Return decrypted operational API credential.
     *
     * @return array{api_key:string,api_secret:string}|null
     */
    public function get(): ?array
    {
        $runtime = $this->runtime->getRuntime();

        if (!is_array($runtime)) {
            return null;
        }

        $metadata = $this->decodeMetadata(
            $runtime['metadata_json'] ?? null
        );

        $credential = $metadata[self::META_KEY] ?? null;

        if (!is_array($credential)) {
            return null;
        }

        foreach ([
            'api_key_enc',
            'api_key_iv',
            'api_key_tag',
            'api_secret_enc',
            'api_secret_iv',
            'api_secret_tag',
        ] as $field) {
            if (
                !isset($credential[$field])
                || trim((string) $credential[$field]) === ''
            ) {
                return null;
            }
        }

        return [
            'api_key' => $this->crypto->decrypt(
                (string) $credential['api_key_enc'],
                (string) $credential['api_key_iv'],
                (string) $credential['api_key_tag']
            ),
            'api_secret' => $this->crypto->decrypt(
                (string) $credential['api_secret_enc'],
                (string) $credential['api_secret_iv'],
                (string) $credential['api_secret_tag']
            ),
        ];
    }

    public function has(): bool
    {
        $credential = $this->get();

        return is_array($credential)
            && $credential['api_key'] !== ''
            && $credential['api_secret'] !== '';
    }

    /**
     * @return array<string,mixed>
     */
    private function decodeMetadata(
        mixed $metadata
    ): array {
        if (
            !is_string($metadata)
            || trim($metadata) === ''
        ) {
            return [];
        }

        $decoded = json_decode(
            $metadata,
            true
        );

        return is_array($decoded)
            ? $decoded
            : [];
    }
}
