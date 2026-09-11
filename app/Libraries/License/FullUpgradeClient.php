<?php

declare(strict_types=1);

namespace App\Libraries\License;

use Config\License;
use RuntimeException;

final class FullUpgradeClient
{
    private License $config;

    public function __construct(
        ?License $config = null
    ) {
        $this->config = $config ?? config('License');

        if ($this->config->baseUrl === '') {
            throw new RuntimeException(
                'LICENSE_BASE_URL is required.'
            );
        }

        if ($this->config->apiKey === '') {
            throw new RuntimeException(
                'LICENSE_API_KEY is required.'
            );
        }

        if ($this->config->appVersion === '') {
            throw new RuntimeException(
                'LICENSE_APP_VERSION is required.'
            );
        }
    }

    /**
     * Resolve the Full operational credential.
     *
     * No API Secret is accepted from configuration.
     */
    public function bootstrap(
        string $licenseKey,
        string $apiKey
    ): array {
        $licenseKey = strtoupper(trim($licenseKey));
        $apiKey = trim($apiKey);

        if ($licenseKey === '') {
            throw new RuntimeException(
                'Full license key is required.'
            );
        }

        if ($apiKey === '') {
            throw new RuntimeException(
                'Full API key is required.'
            );
        }

        $client = service('curlrequest', [
            'baseURI' => $this->config->baseUrl . '/',
            'timeout' => $this->config->timeout,
        ]);

        $response = $client->request(
            'POST',
            'api/v1/credential/bootstrap',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-API-Key' => $apiKey,
                ],
                'body' => json_encode(
                    [
                        'license_key' => $licenseKey,
                    ],
                    JSON_UNESCAPED_SLASHES
                    | JSON_UNESCAPED_UNICODE
                    | JSON_THROW_ON_ERROR
                ),
                'http_errors' => false,
            ]
        );

        $status = $response->getStatusCode();
        $raw = (string) $response->getBody();
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            throw new RuntimeException(
                "License Server returned non-JSON response (HTTP {$status})."
            );
        }

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException(
                (string) (
                    $data['message']
                    ?? "Full credential bootstrap failed (HTTP {$status})."
                )
            );
        }

        if (($data['success'] ?? false) !== true) {
            throw new RuntimeException(
                (string) (
                    $data['message']
                    ?? 'Full credential bootstrap failed.'
                )
            );
        }

        $result = $data['data'] ?? null;

        if (!is_array($result)) {
            throw new RuntimeException(
                'Full credential bootstrap response data is invalid.'
            );
        }

        $responseApiKey = trim(
            (string) ($result['api_key'] ?? '')
        );

        $responseApiSecret = trim(
            (string) ($result['api_secret'] ?? '')
        );

        $licenseType = strtolower(
            trim((string) ($result['license_type'] ?? ''))
        );

        $applicationId = $result['application_id'] ?? null;

        if ($responseApiKey === '') {
            throw new RuntimeException(
                'Full credential bootstrap did not return API key.'
            );
        }

        if ($responseApiSecret === '') {
            throw new RuntimeException(
                'Full credential bootstrap did not return API secret.'
            );
        }

        if ($licenseType !== 'full') {
            throw new RuntimeException(
                'Full credential bootstrap returned invalid license type.'
            );
        }

        if (!hash_equals($apiKey, $responseApiKey)) {
            throw new RuntimeException(
                'Full credential bootstrap API key mismatch.'
            );
        }

        if ($applicationId === null) {
            throw new RuntimeException(
                'Full credential bootstrap returned no application ID.'
            );
        }

        return [
            'api_key' => $responseApiKey,
            'api_secret' => $responseApiSecret,
            'license_type' => $licenseType,
            'application_id' => (int) $applicationId,
            'secret_generated' => (
                ($result['secret_generated'] ?? false) === true
            ),
        ];
    }
}
