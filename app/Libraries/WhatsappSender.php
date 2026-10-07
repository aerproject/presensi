<?php

namespace App\Libraries;

use App\Models\WapikeyModel;

class WhatsappSender
{
    public function send($phone, $message)
    {
        $wapikeyModel = new WapikeyModel();
        $config = $wapikeyModel->getActiveProvider();

        if (!$config) {
            return [false, 'Tidak ada provider aktif'];
        }

        $phone    = preg_replace('/\D/', '', (string) $phone);
        $message  = (string) $message;
        $provider = strtolower(trim((string) ($config['provider'] ?? '')));
        $apiUrl   = rtrim(trim((string) ($config['wa_api_url'] ?? '')), '/');
        $apiKey   = trim((string) ($config['wa_api_key'] ?? ''));

        if (!in_array($provider, ['makesender', 'wisender', 'onesender'], true)) {
            return [false, 'Provider WhatsApp tidak didukung: ' . $provider];
        }

        if ($apiUrl === '') {
            return [false, 'Base URL WhatsApp Gateway belum dikonfigurasi'];
        }

        if ($apiKey === '') {
            return [false, 'WA API Key belum dikonfigurasi'];
        }

        $providers = [
            'onesender' => [
                'endpoint' => $this->buildEndpoint($apiUrl, '/api/v1/messages'),
                'payload'  => [
                    'recipient_type' => 'individual',
                    'to'             => $phone,
                    'type'           => 'text',
                    'text'           => ['body' => $message],
                ],
            ],

            'wisender' => [
                'endpoint' => $this->buildEndpoint($apiUrl, '/api/send-message'),
                'payload'  => [
                    'api_key'  => $apiKey,
                    'receiver' => $phone,
                    'data'     => ['message' => $message],
                ],
            ],

            'makesender' => [
                'endpoint' => $this->buildEndpoint($apiUrl, '/api/v1/messages'),
                'payload'  => [
                    'to'   => $phone,
                    'text' => $message,
                ],
            ],
        ];

        $providerConfig = $providers[$provider];

        return $this->sendCurl(
            $providerConfig['endpoint'],
            $apiKey,
            $providerConfig['payload'],
            $provider,
            $phone,
            $message
        );
    }

    private function buildEndpoint(string $baseUrl, string $endpoint): string
    {
        $baseUrl = rtrim($baseUrl, '/');
        $endpoint = '/' . ltrim($endpoint, '/');

        if (str_ends_with($baseUrl, $endpoint)) {
            return $baseUrl;
        }

        return $baseUrl . $endpoint;
    }

    private function sendCurl($url, $apiKey, $payload, $provider, $phone, $message)
    {
        $headers = $this->buildHeaders($provider, $apiKey);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_CAINFO         => APPPATH . 'Config/certs/cacert.pem',
            CURLOPT_TIMEOUT        => 30,
        ]);

        // Jangan log API key, Bearer token, atau payload yang mengandung credential.
        log_message(
            'debug',
            sprintf(
                'WhatsApp request provider=%s endpoint=%s phone=%s',
                $provider,
                $url,
                $this->maskPhone($phone)
            )
        );

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $execTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            $this->saveLog(
                $provider,
                $phone,
                $message,
                null,
                'failed',
                $error
            );

            return [false, $error];
        }

        curl_close($ch);

        $decoded = json_decode($response, true);

        log_message(
            'debug',
            sprintf(
                'WhatsApp response provider=%s http=%s time=%.3fs',
                $provider,
                $httpCode,
                $execTime
            )
        );

        if ($httpCode === 200 && is_array($decoded)) {
            if (
                $provider === 'makesender'
                && ($decoded['success'] ?? false) === true
                && ($decoded['status'] ?? null) === 'sent'
            ) {
                $this->saveLog(
                    $provider,
                    $phone,
                    $message,
                    $decoded['message_id'] ?? null,
                    'success',
                    null
                );

                return [true, null];
            }

            if (
                $provider === 'wisender'
                && isset($decoded['status'])
                && in_array($decoded['status'], [true, 'success', 'queued'], true)
            ) {
                $this->saveLog(
                    $provider,
                    $phone,
                    $message,
                    $decoded['message_id'] ?? $decoded['id'] ?? null,
                    'success',
                    null
                );

                return [true, null];
            }

            if (
                $provider === 'onesender'
                && isset($decoded['code'])
                && (int) $decoded['code'] === 200
            ) {
                $this->saveLog(
                    $provider,
                    $phone,
                    $message,
                    $decoded['messages'][0]['id'] ?? null,
                    'success',
                    null
                );

                return [true, null];
            }
        }

        $errorMsg = $this->extractErrorMessage($decoded, $httpCode);

        $this->saveLog(
            $provider,
            $phone,
            $message,
            null,
            'failed',
            $errorMsg
        );

        return [false, $errorMsg];
    }

    private function buildHeaders($provider, $apiKey)
    {
        if ($provider === 'onesender') {
            return [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ];
        }

        if ($provider === 'wisender') {
            return [
                'Accept: */*',
                'Content-Type: application/json',
            ];
        }

        return [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-API-Key: ' . $apiKey,
        ];
    }

    private function extractErrorMessage($decoded, $httpCode)
    {
        if (is_array($decoded)) {
            foreach (['error', 'message', 'detail'] as $field) {
                if (isset($decoded[$field]) && is_scalar($decoded[$field])) {
                    return 'HTTP ' . $httpCode . ': ' . (string) $decoded[$field];
                }
            }
        }

        return 'HTTP ' . $httpCode . ': Gateway mengembalikan response yang tidak valid';
    }

    private function maskPhone($phone)
    {
        $length = strlen($phone);

        if ($length <= 6) {
            return str_repeat('*', $length);
        }

        return substr($phone, 0, 4)
            . str_repeat('*', max(0, $length - 7))
            . substr($phone, -3);
    }

    private function saveLog($provider, $phone, $message, $messageId, $status, $errorMessage)
    {
        $db = \Config\Database::connect();

        $db->table('wa_message_logs')->insert([
            'provider'      => $provider,
            'phone'         => $phone,
            'message'       => $message,
            'message_id'    => $messageId,
            'status'        => $status,
            'error_message' => $errorMessage,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
