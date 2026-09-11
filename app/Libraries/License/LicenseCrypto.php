<?php

declare(strict_types=1);

namespace App\Libraries\License;

use Config\License;
use RuntimeException;

final class LicenseCrypto
{
    private License $config;

    public function __construct(?License $config = null)
    {
        $this->config = $config ?? config('License');
    }

    private function key(): string
    {
        return hash(
            'sha256',
            (new LocalKeyManager())->getKey(),
            true
        );
    }

    /**
     * Encrypt license data with AES-256-GCM.
     *
     * @return array{
     *     ciphertext: string,
     *     iv: string,
     *     tag: string
     * }
     */
    public function encrypt(string $plain): array
    {
        $iv = random_bytes(12);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plain,
            'aes-256-gcm',
            $this->key(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($ciphertext === false) {
            throw new RuntimeException(
                'License encryption failed.'
            );
        }

        return [
            'ciphertext' => base64_encode($ciphertext),
            'iv'         => base64_encode($iv),
            'tag'        => base64_encode($tag),
        ];
    }

    public function decrypt(
        string $ciphertextBase64,
        string $ivBase64,
        string $tagBase64
    ): string {
        $ciphertext = base64_decode(
            $ciphertextBase64,
            true
        );

        $iv = base64_decode(
            $ivBase64,
            true
        );

        $tag = base64_decode(
            $tagBase64,
            true
        );

        if (
            $ciphertext === false
            || $iv === false
            || $tag === false
        ) {
            throw new RuntimeException(
                'Invalid encrypted license payload.'
            );
        }

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $this->key(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            throw new RuntimeException(
                'License decryption failed.'
            );
        }

        return $plaintext;
    }
}
