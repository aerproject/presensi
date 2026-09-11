<?php

declare(strict_types=1);

namespace App\Libraries\License;

use Config\License;
use RuntimeException;

final class TrialBootstrapClient
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

    public function bootstrap(
        string $licenseKey,
        string $apiKey,
        bool $allowExistingServer = false
    ): array {

        log_message(
            'error',
            'TRIAL_BOOTSTRAP_ENTERED'
        );

        if (!$allowExistingServer) {

            $guard = new TrialReinstallGuard(
                new \App\Models\LicenseRuntimeModel()
            );

            log_message(
                'error',
                'TRIAL_BOOTSTRAP_GUARD_ALLOWED=' .
                ($guard->isAllowed() ? 'YES' : 'NO')
            );

            if (!$guard->isAllowed()) {
                throw new RuntimeException(
                    $guard->notice()
                    ?? 'Trial installation is not allowed on this server.'
                );
            }
        }

        $licenseKey = strtoupper(trim($licenseKey));
        $apiKey = trim($apiKey);

        if ($licenseKey === '') {
            throw new RuntimeException(
                'Trial license key is required.'
            );
        }

        if ($apiKey === '') {
            throw new RuntimeException(
                'Trial API key is required.'
            );
        }

        $client = service('curlrequest', [
            'baseURI' => $this->config->baseUrl . '/',
            'timeout' => $this->config->timeout,
        ]);

        try {

            $response = $client->request(
                'POST',
                'api/v1/trial/bootstrap',
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

        } catch (\Throwable $e) {

            log_message(
                'error',
                'TRIAL_BOOTSTRAP_REQUEST_EXCEPTION=' . $e->getMessage()
            );

            throw $e;
        }

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
                    ?? "Trial bootstrap failed (HTTP {$status})."
                )
            );
        }

        if (($data['success'] ?? false) !== true) {
            throw new RuntimeException(
                (string) (
                    $data['message']
                    ?? 'Trial bootstrap failed.'
                )
            );
        }

        $result = $data['data'] ?? null;

        if (!is_array($result)) {
            throw new RuntimeException(
                'Trial bootstrap response data is invalid.'
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

        if ($responseApiKey === '') {
            throw new RuntimeException(
                'Trial bootstrap did not return API key.'
            );
        }

        if ($responseApiSecret === '') {
            throw new RuntimeException(
                'Trial bootstrap did not return API secret.'
            );
        }

        if ($licenseType !== 'trial') {
            throw new RuntimeException(
                'Trial bootstrap returned invalid license type.'
            );
        }

        if (!hash_equals($apiKey, $responseApiKey)) {
            throw new RuntimeException(
                'Trial bootstrap API key mismatch.'
            );
        }

        return [
            'api_key' => $responseApiKey,
            'api_secret' => $responseApiSecret,
            'license_type' => $licenseType,
            'secret_generated' => (
                ($result['secret_generated'] ?? false) === true
            ),
        ];
    }
}
