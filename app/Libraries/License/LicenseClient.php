<?php

declare(strict_types=1);

namespace App\Libraries\License;

use Config\License;
use App\Models\LicenseRuntimeModel;
use RuntimeException;

final class LicenseClient
{
    private License $config;

    private string $apiKey;

    private string $apiSecret;

    public function __construct(
        ?License $config = null,
        ?string $apiKey = null,
        ?string $apiSecret = null
    ) {
        $this->config = $config ?? config('License');

        $this->apiKey = trim(
            (string) ($apiKey ?? '')
        );

        $this->apiSecret = trim(
            (string) ($apiSecret ?? '')
        );

        $this->resolveCredential();

        if ($this->config->baseUrl === '') {
            throw new RuntimeException(
                'LICENSE_BASE_URL is required.'
            );
        }

        if ($this->apiKey === '') {
            throw new RuntimeException(
                'LICENSE_API_KEY is required.'
            );
        }

        if ($this->apiSecret === '') {
            throw new RuntimeException(
                'License API credential is not available.'
            );
        }

        if ($this->config->appVersion === '') {
            throw new RuntimeException(
                'LICENSE_APP_VERSION is required.'
            );
        }
    }

    private function resolveCredential(): void
    {
        if (
            $this->apiKey !== ''
            &&
            $this->apiSecret !== ''
        ) {
            return;
        }

        $store = new LicenseCredentialStore(
            new LicenseRuntimeModel(),
            new LicenseCrypto()
        );

        $credential = $store->get();

        if (!is_array($credential)) {
            return;
        }

        if ($this->apiKey === '') {
            $this->apiKey = trim(
                (string) ($credential['api_key'] ?? '')
            );
        }

        if ($this->apiSecret === '') {
            $this->apiSecret = trim(
                (string) ($credential['api_secret'] ?? '')
            );
        }
    }


    private function sortRecursively(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        if (array_is_list($value)) {
            return array_map(
                fn (mixed $item): mixed => $this->sortRecursively($item),
                $value
            );
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->sortRecursively($item);
        }

        ksort($value);

        return $value;
    }

    private function canonicalize(array $payload): string
    {
        return json_encode(
            $this->sortRecursively($payload),
            JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_THROW_ON_ERROR
        );
    }

    public function sign(
        string $method,
        string $uri,
        string $timestamp,
        string $nonce,
        array $query = [],
        array $body = []
    ): string {
        $payload = [
            'method'      => strtoupper($method),
            'uri'         => trim($uri, '/'),
            'timestamp'   => (string) $timestamp,
            'nonce'       => $nonce,
            'app_version' => $this->config->appVersion,
            'query'       => $query,
            'body'        => $body,
        ];

        return hash_hmac(
            'sha256',
            $this->canonicalize($payload),
            $this->apiSecret
        );
    }

    private function generateNonce(): string
    {
        return bin2hex(random_bytes(24));
    }

    public function request(
        string $method,
        string $uri,
        array $body = [],
        array $query = []
    ): array {
        $timestamp = (string) time();
        $nonce = $this->generateNonce();

        $signature = $this->sign(
            $method,
            $uri,
            $timestamp,
            $nonce,
            $query,
            $body
        );

        $client = service('curlrequest', [
            'baseURI' => $this->config->baseUrl . '/',
            'timeout' => $this->config->timeout,
        ]);

        $response = $client->request(
            strtoupper($method),
            ltrim($uri, '/'),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-API-Key' => $this->apiKey,
                    'X-Timestamp' => $timestamp,
                    'X-Nonce' => $nonce,
                    'X-App-Version' => $this->config->appVersion,
                    'X-Signature' => $signature,
                ],
                'query' => $query,
                'body' => json_encode(
                    $body,
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
                    ?? "License request failed (HTTP {$status})."
                )
            );
        }

        return $data;
    }

    public function activate(array $payload): array
    {
        return $this->request(
            'POST',
            'api/v1/activation',
            $payload
        );
    }

    public function validate(array $payload): array
    {
        return $this->request(
            'POST',
            'api/v1/validation',
            $payload
        );
    }

    public function heartbeat(array $payload): array
    {
        return $this->request(
            'POST',
            'api/v1/heartbeat',
            $payload
        );
    }

    /**
     * Submit a Full license renewal request.
     */
    public function renewalRequest(array $payload): array
    {
        return $this->request(
            'POST',
            'api/v1/renewal/request',
            $payload
        );
    }
}
