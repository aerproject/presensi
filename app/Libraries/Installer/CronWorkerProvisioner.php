<?php

declare(strict_types=1);

namespace App\Libraries\Installer;

use RuntimeException;

final class CronWorkerProvisioner
{
    private const CRONTAB = '/usr/bin/crontab';
    private const PHP = '/www/server/php/83/bin/php';

    public function provision(): void
    {
        $projectPath = escapeshellarg(
            rtrim(ROOTPATH, '/')
        );

        $entry = '* * * * * cd ' . $projectPath
            . ' && ' . escapeshellarg(self::PHP)
            . ' spark messages:process-pending'
            . ' >> writable/logs/message-worker.log 2>&1';

        $current = $this->run([self::CRONTAB, '-l'], true);

        $entryExists = false;

        foreach (preg_split('/\\R/', $current) ?: [] as $line) {
            if (
                str_contains(
                    $line,
                    'spark messages:process-pending'
                )
                && str_contains(
                    $line,
                    'message-worker.log'
                )
            ) {
                $entryExists = true;
                break;
            }
        }

        if ($entryExists) {
            return;
        }

        $content = rtrim($current);

        if ($content !== '') {
            $content .= PHP_EOL;
        }

        $content .= $entry . PHP_EOL;

        $this->run(
            [self::CRONTAB, '-'],
            false,
            $content
        );
    }

    private function run(
        array $command,
        bool $allowNoCrontab = false,
        ?string $stdin = null
    ): string {
        if (! function_exists('proc_open')) {
            throw new RuntimeException(
                'proc_open tidak tersedia untuk provisioning worker.'
            );
        }

        $pipes = [];

        $process = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );

        if (! is_resource($process)) {
            throw new RuntimeException(
                'Gagal menjalankan proses provisioning worker.'
            );
        }

        if ($stdin !== null) {
            fwrite($pipes[0], $stdin);
        }

        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if (
            $exitCode !== 0
            && ! (
                $allowNoCrontab
                && str_contains(
                    strtolower((string) $stderr),
                    'no crontab for'
                )
            )
        ) {
            throw new RuntimeException(
                'Provisioning worker gagal: '
                . trim((string) $stderr)
            );
        }

        return (string) $stdout;
    }
}
