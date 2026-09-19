<?php

declare(strict_types=1);

namespace App\Libraries\Installer;

use RuntimeException;

final class EnvWriter
{
    /**
     * Update installer-managed values without destroying
     * unrelated application configuration.
     *
     * Existing .env is preserved.
     * If .env does not exist, .env.example is used as template.
     */
    public function write(array $values): void
    {
        $envPath = ROOTPATH . '.env';
        $templatePath = ROOTPATH . '.env.example';
        $stagingDir = WRITEPATH . 'installation';

        if (
            ! is_dir($stagingDir)
            && ! mkdir($stagingDir, 0750, true)
            && ! is_dir($stagingDir)
        ) {
            throw new RuntimeException(
                'Unable to create Installer staging directory.'
            );
        }

        if (is_file($envPath) && is_readable($envPath)) {
            $content = file_get_contents($envPath);
        } elseif (is_file($templatePath) && is_readable($templatePath)) {
            $content = file_get_contents($templatePath);
        } else {
            throw new RuntimeException(
                'Neither .env nor .env.example is available.'
            );
        }

        if ($content === false) {
            throw new RuntimeException(
                'Unable to read environment configuration.'
            );
        }

        $content = $this->updateValues($content, $values);

        $temporary = $stagingDir
            . '/.env.'
            . bin2hex(random_bytes(8))
            . '.tmp';

        $written = file_put_contents(
            $temporary,
            $content,
            LOCK_EX
        );

        if ($written === false) {
            throw new RuntimeException(
                'Unable to write staged .env file.'
            );
        }

        @chmod($temporary, 0640);

        if (! is_writable(dirname($envPath))) {
            @unlink($temporary);

            throw new RuntimeException(
                'Application root is not writable by the Installer process.'
            );
        }

        if (! rename($temporary, $envPath)) {
            @unlink($temporary);

            throw new RuntimeException(
                'Unable to finalize .env file.'
            );
        }

        @chmod($envPath, 0640);
    }

    private function updateValues(
        string $content,
        array $values
    ): string {
        foreach ($values as $key => $value) {
            $key = trim((string) $key);

            if ($key === '') {
                continue;
            }

            $line = $key . ' = ' . $this->formatValue($value);

            $pattern = '/^'
                . preg_quote($key, '/')
                . '[ \t]*=.*$/m';

            if (preg_match($pattern, $content) === 1) {
                $content = (string) preg_replace(
                    $pattern,
                    $line,
                    $content,
                    1
                );
                continue;
            }

            $content = rtrim($content) . PHP_EOL
                . $line . PHP_EOL;
        }

        return $content;
    }

    private function formatValue(mixed $value): string
    {
        $value = str_replace(
            ["\r", "\n"],
            '',
            (string) $value
        );

        if ($value === '') {
            return '';
        }

        /*
         * Quote values containing characters that can be interpreted
         * by the dotenv parser.
         */
        if (preg_match('/[\s#\'"=]/', $value) === 1) {
            return "'" . str_replace(
                "'",
                "\\'",
                $value
            ) . "'";
        }

        return $value;
    }
}
