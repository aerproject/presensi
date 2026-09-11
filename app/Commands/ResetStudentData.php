<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class ResetStudentData extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'maintenance:reset-student-data';
    protected $description = 'Reset student/parent operational data while preserving admin and master configuration.';

    public function run(array $params)
    {
        $db = Database::connect();

        /*
        |--------------------------------------------------------------------------
        | GUARD
        |--------------------------------------------------------------------------
        */
        $adminCount = (int) $db->table('users')
            ->where('role', 'admin')
            ->countAllResults();

        if ($adminCount !== 1) {
            CLI::error(
                'ABORT: ADMIN_COUNT harus tepat 1. Ditemukan: ' . $adminCount
            );

            return EXIT_ERROR;
        }

        /*
        |--------------------------------------------------------------------------
        | SNAPSHOT COUNTS BEFORE
        |--------------------------------------------------------------------------
        */
        $deleteTables = [
            'attendance',
            'izin',
            'surat',
            'messages',
            'qrcodes',
            'wa_message_logs',
            'broadcast',
            'plot_kelas',
            'siswa_akademik',
            'parents',
            'siswa',
        ];

        $before = [];

        foreach ($deleteTables as $table) {
            $before[$table] = (int) $db->table($table)->countAllResults();
        }

        $before['users_non_admin'] = (int) $db->table('users')
            ->where('role !=', 'admin')
            ->countAllResults();

        CLI::write('===== RESET START =====');
        CLI::write('ADMIN_COUNT=' . $adminCount);

        foreach ($before as $table => $count) {
            CLI::write($table . '_BEFORE=' . $count);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */
        $db->transBegin();

        try {
            /*
            | Anak/transaksi terlebih dahulu.
            */
            foreach ([
                'attendance',
                'izin',
                'surat',
                'messages',
                'qrcodes',
                'wa_message_logs',
                'broadcast',
            ] as $table) {
                $db->table($table)->delete();
            }

            /*
            | Relasi akademik siswa.
            */
            foreach ([
                'plot_kelas',
                'siswa_akademik',
            ] as $table) {
                $db->table($table)->delete();
            }

            /*
            | Master operasional siswa.
            */
            $db->table('parents')->delete();
            $db->table('siswa')->delete();

            /*
            | Semua user siswa + ortu, admin tetap.
            */
            $db->table('users')
                ->whereIn('role', ['siswa', 'ortu'])
                ->delete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Transaksi database melaporkan kegagalan.'
                );
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();

            CLI::error(
                'RESET GAGAL: ' . $e->getMessage()
            );

            return EXIT_ERROR;
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY
        |--------------------------------------------------------------------------
        */
        CLI::write('===== VERIFY =====');

        foreach ($deleteTables as $table) {
            $count = (int) $db->table($table)->countAllResults();

            CLI::write(
                $table . '_AFTER=' . $count
            );

            if ($count !== 0) {
                CLI::error(
                    'VERIFY FAILED: ' . $table . ' masih berisi data.'
                );

                return EXIT_ERROR;
            }
        }

        $nonAdminAfter = (int) $db->table('users')
            ->where('role !=', 'admin')
            ->countAllResults();

        $adminAfter = (int) $db->table('users')
            ->where('role', 'admin')
            ->countAllResults();

        CLI::write(
            'users_non_admin_AFTER=' . $nonAdminAfter
        );

        CLI::write(
            'admin_count_AFTER=' . $adminAfter
        );

        if ($nonAdminAfter !== 0 || $adminAfter !== 1) {
            CLI::error(
                'VERIFY FAILED: kondisi users tidak sesuai.'
            );

            return EXIT_ERROR;
        }

        /*
        |--------------------------------------------------------------------------
        | PRESERVE CHECK
        |--------------------------------------------------------------------------
        */
        $preserveTables = [
            'guru',
            'walikelas',
            'kelas',
            'jurusan',
            'tapel',
            'jam_belajar',
            'hari_kerja',
            'libursekolah',
            'pengaturan',
            'status_absensi',
            'status_akademik',
            'status_siswa',
            'wapikey',
        ];

        CLI::write('===== PRESERVED TABLES =====');

        foreach ($preserveTables as $table) {
            CLI::write(
                $table . '_AFTER=' .
                $db->table($table)->countAllResults()
            );
        }

        CLI::write('RESET_STATUS=PASS');

        return EXIT_SUCCESS;
    }
}
