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
            return [false, "Tidak ada provider aktif"];
        }

        // ✅ Sanitasi nomor telepon (hanya angka)
        $phone = preg_replace('/\D/', '', $phone);

        $provider = strtolower($config['provider']);
        $apiUrl   = rtrim($config['wa_api_url'], '/');
        $apiKey   = $config['wa_api_key'];

        // ✅ Mapping provider agar lebih DRY
        $providers = [
            'onesender' => [
                'endpoint' => $apiUrl . '/api/v1/messages',
                'payload'  => [
                    "recipient_type" => "individual",
                    "to"             => $phone,
                    "type"           => "text",
                    "text"           => ["body" => $message]
                ]
            ],
            'wisender' => [
                'endpoint' => $apiUrl . '/api/send-message',
                'payload'  => [
                    "api_key"  => $apiKey,
                    "receiver" => $phone,
                    "data"     => ["message" => $message]
                ]
            ],
            'makesender' => [
                'endpoint' => $apiUrl . '/api/v1/messages',
                'payload'  => [
                    "to"   => $phone,
                    "text" => $message
                ]
            ],
            'default' => [
                'endpoint' => $apiUrl,
                'payload'  => [
                    "api_key"  => $apiKey,
                    "receiver" => $phone,
                    "message"  => $message
                ]
            ]
        ];

        $configProvider = $providers[$provider] ?? $providers['default'];

        return $this->sendCurl(
            $configProvider['endpoint'],
            $apiKey,
            $configProvider['payload'],
            $provider,
            $phone
        );
    }

    private function sendCurl($url, $apiKey, $payload, $provider, $phone)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $this->buildHeaders($provider, $apiKey),
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_CAINFO         => APPPATH . 'Config/certs/cacert.pem',
            CURLOPT_TIMEOUT        => 30, // ✅ timeout agar tidak menggantung
        ]);

        log_message('debug', 'Endpoint: ' . $url);
        log_message('debug', 'Payload: ' . json_encode($payload));
        log_message('debug', 'Headers: ' . json_encode($this->buildHeaders($provider, $apiKey)));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $execTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            $this->saveLog($provider, $phone, $payload['text']['body'] ?? $payload['message'] ?? '', null, 'failed', $error);
            return [false, $error];
        }

        curl_close($ch);

        $decoded = json_decode($response, true);
        log_message('debug', "Response ({$httpCode}, {$execTime}s): " . $response);

        if ($httpCode === 200) {
            // MakeSender
            if (
                $provider === 'makesender' &&
                isset($decoded['success']) &&
                $decoded['success'] === true &&
                ($decoded['status'] ?? null) === 'sent'
            ) {
                $msgId = $decoded['message_id'] ?? null;

                $this->saveLog(
                    $provider,
                    $phone,
                    $payload['text'] ?? '',
                    $msgId,
                    'success',
                    null
                );

                return [true, null];
            }

            // Wisender
            if (isset($decoded['status']) && ($decoded['status'] === true || $decoded['status'] === 'success' || $decoded['status'] === 'queued')) {
                $this->saveLog($provider, $phone, $payload['data']['message'], null, 'success', null);
                return [true, null];
            }
            // OneSender
            if (isset($decoded['code']) && $decoded['code'] === 200) {
                $msgId = $decoded['messages'][0]['id'] ?? null;
                $this->saveLog($provider, $phone, $payload['text']['body'], $msgId, 'success', null);
                return [true, null];
            }
        }

        $errorMsg = $decoded['error'] ?? $decoded['message'] ?? "HTTP $httpCode: $response";
        $this->saveLog($provider, $phone, $payload['text']['body'] ?? $payload['message'] ?? '', null, 'failed', "HTTP $httpCode: $errorMsg");
        return [false, $errorMsg];
    }

    private function buildHeaders($provider, $apiKey)
    {
        if ($provider === 'onesender') {
            return [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ];
        }

        if ($provider === 'wisender') {
            return [
                'Accept: */*',
                'Content-Type: application/json'
            ];
        }

        if ($provider === 'makesender') {
        return [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-API-Key: ' . $apiKey
        ];
    }

    return ['Content-Type: application/json'];
    }

    private function saveLog($provider, $phone, $message, $messageId, $status, $errorMessage)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('wa_message_logs');
        $builder->insert([
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
