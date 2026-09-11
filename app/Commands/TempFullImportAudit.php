<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class TempFullImportAudit extends BaseCommand
{
    protected $group = 'Temp';
    protected $name = 'temp:full-import-audit';
    protected $description = 'Audit full CSV against current master database without importing.';

    public function run(array $params)
    {
        $csvPath = $params[0] ?? '';

        if ($csvPath === '' || !is_file($csvPath)) {
            CLI::error('Gunakan: php spark temp:full-import-audit /path/file.csv');
            return EXIT_ERROR;
        }

        $handle = fopen($csvPath, 'r');

        if ($handle === false) {
            CLI::error('CSV tidak dapat dibuka.');
            return EXIT_ERROR;
        }

        $header = fgetcsv($handle, 0, ';');

        if ($header === false) {
            fclose($handle);
            CLI::error('Header CSV tidak ditemukan.');
            return EXIT_ERROR;
        }

        $header = array_map(
            fn ($h) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', $h))),
            $header
        );

        $db = Database::connect();

        $total = 0;
        $valid = 0;
        $invalid = 0;
        $existing = 0;
        $new = 0;

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            if (empty(array_filter($line))) {
                continue;
            }

            $total++;

            if (count($line) !== count($header)) {
                $invalid++;
                continue;
            }

            $data = array_combine($header, array_map('trim', $line));

            $invalidWa = false;

            foreach (['wa_siswa', 'wa_ortu'] as $field) {
                $value = trim((string) ($data[$field] ?? ''));

                if ($value === '') {
                    continue;
                }

                if (
                    preg_match(
                        '/^[+-]?\d+(?:[.,]\d+)?[Ee][+-]?\d+$/i',
                        $value
                    )
                ) {
                    $invalidWa = true;
                    break;
                }

                $normalized = preg_replace(
                    '/[\s\-\(\)\.]/',
                    '',
                    $value
                );

                if (strpos($normalized, '+62') === 0) {
                    $normalized = '62' . substr($normalized, 3);
                } elseif (strpos($normalized, '08') === 0) {
                    $normalized = '62' . substr($normalized, 1);
                }

                if (
                    !preg_match('/^62\d+$/', $normalized) ||
                    strlen($normalized) < 10 ||
                    strlen($normalized) > 15
                ) {
                    $invalidWa = true;
                    break;
                }
            }

            if ($invalidWa) {
                $invalid++;
                continue;
            }

            $valid++;

            $nis = trim((string) ($data['nis'] ?? ''));

            $exists = $db->table('siswa')
                ->where('nis', $nis)
                ->countAllResults();

            if ($exists) {
                $existing++;
            } else {
                $new++;
                CLI::write(
                    'NEW_VALID | NIS=' . $nis .
                    ' | NAMA=' . ($data['nama_siswa'] ?? '')
                );
            }
        }

        fclose($handle);

        CLI::write('===== SUMMARY =====');
        CLI::write('TOTAL=' . $total);
        CLI::write('VALID=' . $valid);
        CLI::write('INVALID=' . $invalid);
        CLI::write('VALID_EXISTING=' . $existing);
        CLI::write('VALID_NEW=' . $new);

        return EXIT_SUCCESS;
    }
}
