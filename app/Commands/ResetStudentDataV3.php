<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class ResetStudentDataV3 extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'maintenance:reset-student-data-v3';
    protected $description = 'Reset student-related data across MyISAM and InnoDB tables.';

    public function run(array $params)
    {
        $db = Database::connect();

        $adminCount = (int) $db->query(
            "SELECT COUNT(*) AS total
             FROM users
             WHERE role = 'admin'"
        )->getRowArray()['total'];

        $nonAdminCount = (int) $db->query(
            "SELECT COUNT(*) AS total
             FROM users
             WHERE role IN ('siswa','ortu')"
        )->getRowArray()['total'];

        $siswaCount = (int) $db->query(
            "SELECT COUNT(*) AS total FROM siswa"
        )->getRowArray()['total'];

        if ($adminCount !== 1) {
            CLI::error(
                'ABORT: admin harus tepat 1. Ditemukan=' . $adminCount
            );
            return EXIT_ERROR;
        }

        if ($nonAdminCount === 0 && $siswaCount === 0) {
            CLI::error(
                'ABORT: data siswa sudah kosong.'
            );
            return EXIT_ERROR;
        }

        CLI::write('===== RESET V3 =====');
        CLI::write('ADMIN_COUNT=' . $adminCount);
        CLI::write('NON_ADMIN_USERS=' . $nonAdminCount);
        CLI::write('SISWA=' . $siswaCount);

        /*
        |--------------------------------------------------------------------------
        | MYISAM TABLES
        |--------------------------------------------------------------------------
        |
        | Tidak transactional. Hapus secara eksplisit dan verifikasi.
        |
        */
        $myisamTables = [
            'attendance' => false, // sebenarnya InnoDB; dipisah di bawah
            'izin' => true,
            'surat' => true,
            'messages' => true,
            'qrcodes' => true,
            'wa_message_logs' => true,
            'broadcast' => true,
            'parents' => true,
        ];

        foreach ($myisamTables as $table => $isMyisam) {
            if (!$isMyisam) {
                continue;
            }

            CLI::write('DELETE ' . $table);

            $db->query('DELETE FROM ' . $table);

            $remaining = (int) $db->query(
                'SELECT COUNT(*) AS total FROM ' . $table
            )->getRowArray()['total'];

            CLI::write(
                'VERIFY_' . $table . '=' . $remaining
            );

            if ($remaining !== 0) {
                CLI::error(
                    'ABORT: ' . $table . ' tidak berhasil dikosongkan.'
                );
                return EXIT_ERROR;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INNODB DEPENDENCY ORDER
        |--------------------------------------------------------------------------
        */
        $innodbTables = [
            'attendance',
            'plot_kelas',
            'siswa_akademik',
            'siswa',
        ];

        foreach ($innodbTables as $table) {
            CLI::write('DELETE ' . $table);

            $db->query('DELETE FROM ' . $table);

            $remaining = (int) $db->query(
                'SELECT COUNT(*) AS total FROM ' . $table
            )->getRowArray()['total'];

            CLI::write(
                'VERIFY_' . $table . '=' . $remaining
            );

            if ($remaining !== 0) {
                CLI::error(
                    'ABORT: ' . $table . ' tidak berhasil dikosongkan.'
                );
                return EXIT_ERROR;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        CLI::write('DELETE users siswa/ortu');

        $db->query(
            "DELETE FROM users
             WHERE role IN ('siswa','ortu')"
        );

        $remainingUsers = (int) $db->query(
            "SELECT COUNT(*) AS total
             FROM users
             WHERE role IN ('siswa','ortu')"
        )->getRowArray()['total'];

        $remainingAdmin = (int) $db->query(
            "SELECT COUNT(*) AS total
             FROM users
             WHERE role = 'admin'"
        )->getRowArray()['total'];

        CLI::write(
            'VERIFY_USERS_NON_ADMIN=' . $remainingUsers
        );

        CLI::write(
            'VERIFY_ADMIN=' . $remainingAdmin
        );

        if ($remainingUsers !== 0 || $remainingAdmin !== 1) {
            CLI::error(
                'ABORT: users verification gagal.'
            );
            return EXIT_ERROR;
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL
        |--------------------------------------------------------------------------
        */
        CLI::write('===== RESET V3 FINAL VERIFY =====');

        foreach ([
            'attendance',
            'izin',
            'surat',
            'messages',
            'qrcodes',
            'wa_message_logs',
            'broadcast',
            'parents',
            'plot_kelas',
            'siswa_akademik',
            'siswa',
        ] as $table) {
            $count = (int) $db->query(
                'SELECT COUNT(*) AS total FROM ' . $table
            )->getRowArray()['total'];

            CLI::write(
                $table . '=' . $count
            );

            if ($count !== 0) {
                CLI::error(
                    'FINAL VERIFY FAILED: ' . $table
                );
                return EXIT_ERROR;
            }
        }

        CLI::write(
            'users_non_admin=' .
            $remainingUsers
        );

        CLI::write(
            'admin=' .
            $remainingAdmin
        );

        CLI::write('RESET_V3_STATUS=PASS');

        return EXIT_SUCCESS;
    }
}
