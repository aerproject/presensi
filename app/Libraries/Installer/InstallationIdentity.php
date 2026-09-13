<?php

declare(strict_types=1);

namespace App\Libraries\Installer;

use RuntimeException;

final class InstallationIdentity
{
    /**
     * Local installation identity directory.
     *
     * This location is intentionally outside
     * the application database.
     */
    private const DIRECTORY = 'installation';

    /**
     * Stable UUID filename.
     */
    private const UUID_FILE = 'installation_uuid';

    /**
     * Return the stable UUID for this
     * PRESENSI application installation.
     *
     * If no UUID exists yet, a new UUID v4
     * is generated and persisted locally.
     *
     * This UUID identifies the application
     * installation, NOT the physical server.
     */
    public function uuid(): string
    {
        $path = $this->uuidPath();

        if (is_file($path)) {
            $uuid = trim(
                (string) file_get_contents($path)
            );

            if ($this->isValidUuid($uuid)) {
                return strtolower($uuid);
            }
        }

        $uuid = $this->generateUuid();

        $this->store($uuid);

        return $uuid;
    }

    /**
     * Return UUID only when already present.
     *
     * This method never generates a UUID.
     */
    public function existingUuid(): ?string
    {
        $path = $this->uuidPath();

        if (!is_file($path)) {
            return null;
        }

        $uuid = trim(
            (string) file_get_contents($path)
        );

        if (!$this->isValidUuid($uuid)) {
            return null;
        }

        return strtolower($uuid);
    }

    /**
     * Determine whether this application
     * installation already has an identity.
     */
    public function exists(): bool
    {
        return $this->existingUuid() !== null;
    }

    /**
     * Persist UUID locally.
     *
     * Existing valid UUID is never replaced.
     */
    private function store(
        string $uuid
    ): void {
        if (!$this->isValidUuid($uuid)) {
            throw new RuntimeException(
                'Installation UUID is invalid.'
            );
        }

        $directory = $this->directoryPath();

        if (
            !is_dir($directory)
            &&
            !mkdir(
                $directory,
                0750,
                true
            )
            &&
            !is_dir($directory)
        ) {
            throw new RuntimeException(
                'Unable to create installation identity directory.'
            );
        }

        $path = $this->uuidPath();

        /*
         * Protect an existing valid identity.
         */
        if (is_file($path)) {
            $existing = trim(
                (string) file_get_contents($path)
            );

            if ($this->isValidUuid($existing)) {
                return;
            }
        }

        $temporary = $path . '.tmp';

        $written = file_put_contents(
            $temporary,
            $uuid . PHP_EOL,
            LOCK_EX
        );

        if ($written === false) {
            throw new RuntimeException(
                'Unable to write Installation UUID.'
            );
        }

        if (!rename($temporary, $path)) {
            @unlink($temporary);

            throw new RuntimeException(
                'Unable to finalize Installation UUID.'
            );
        }

        @chmod($path, 0640);
    }

    /**
     * Generate RFC 4122 UUID v4.
     */
    private function generateUuid(): string
    {
        $bytes = random_bytes(16);

        $bytes[6] = chr(
            (ord($bytes[6]) & 0x0f) | 0x40
        );

        $bytes[8] = chr(
            (ord($bytes[8]) & 0x3f) | 0x80
        );

        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    /**
     * Validate UUID v4 format.
     */
    private function isValidUuid(
        string $uuid
    ): bool {
        return (bool) preg_match(
            '/^[0-9a-f]{8}-'
            . '[0-9a-f]{4}-'
            . '4[0-9a-f]{3}-'
            . '[89ab][0-9a-f]{3}-'
            . '[0-9a-f]{12}$/i',
            $uuid
        );
    }

    /**
     * Return installation identity directory.
     */
    private function directoryPath(): string
    {
        /*
         * Use CodeIgniter WRITEPATH when available.
         *
         * During standalone installer execution,
         * CodeIgniter may not be bootstrapped yet.
         * In that case resolve writable/ from
         * the application project root.
         */
        if (defined('WRITEPATH')) {
            $writePath = (string) constant('WRITEPATH');
        } else {
            $writePath = dirname(__DIR__, 3)
                . DIRECTORY_SEPARATOR
                . 'writable';
        }

        return rtrim(
            $writePath,
            DIRECTORY_SEPARATOR
        )
        . DIRECTORY_SEPARATOR
        . self::DIRECTORY;
    }

    /**
     * Return UUID storage path.
     */
    private function uuidPath(): string
    {
        return $this->directoryPath()
            . DIRECTORY_SEPARATOR
            . self::UUID_FILE;
    }
}
