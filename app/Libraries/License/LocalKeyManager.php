<?php

declare(strict_types=1);

namespace App\Libraries\License;

use RuntimeException;

final class LocalKeyManager
{
    private string $path;

    public function __construct(
        ?string $path = null
    ) {
        $this->path =
            $path
            ??
            WRITEPATH . 'license/encryption.key';
    }

    public function getKey(): string
    {
        if (is_file($this->path)) {
            $key = trim(
                (string) file_get_contents($this->path)
            );

            if ($key !== '') {
                return $key;
            }
        }

        return $this->generate();
    }

    private function generate(): string
    {
        $directory = dirname($this->path);

        if (!is_dir($directory)) {
            mkdir(
                $directory,
                0750,
                true
            );
        }

        $key = bin2hex(
            random_bytes(32)
        );

        file_put_contents(
            $this->path,
            $key,
            LOCK_EX
        );

        chmod(
            $this->path,
            0640
        );

        return $key;
    }
}
