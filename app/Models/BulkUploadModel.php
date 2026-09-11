<?php

namespace App\Models;

use CodeIgniter\Model;

class BulkUploadModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function bulkInsert(array $rows): bool
    {
        if (empty($rows)) {
            return true;
        }

        $this->db->transBegin();

        try {
            $usersTable   = $this->db->table('users');
            $parentsTable = $this->db->table('parents');
            $siswaTable   = $this->db->table('siswa');

            /*
            |--------------------------------------------------------------------------
            | 1. INSERT USERS TANPA MENGANDALKAN URUTAN AUTO_INCREMENT
            |--------------------------------------------------------------------------
            */
            $usersInsert = [];

            foreach ($rows as $data) {
                $nis = trim((string) ($data['nis'] ?? ''));

                if ($nis === '') {
                    throw new \RuntimeException('NIS kosong ditemukan pada data import.');
                }

                $usersInsert[] = [
                    'username' => $nis,
                    'email'    => !empty($data['email'])
                        ? $data['email']
                        : $nis . '@dummy.local',
                    'password' => password_hash(
                        $data['password'] ?? '123456',
                        PASSWORD_DEFAULT
                    ),
                    'role'     => 'siswa',
                ];

                $usersInsert[] = [
                    'username' => $nis . 'ortu',
                    'email'    => !empty($data['email_ortu'])
                        ? $data['email_ortu']
                        : $nis . 'ortu@dummy.local',
                    'password' => password_hash(
                        $data['password_ortu'] ?? '123456',
                        PASSWORD_DEFAULT
                    ),
                    'role'     => 'ortu',
                ];
            }

            if (!empty($usersInsert)) {
                $usersTable->insertBatch($usersInsert);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. AMBIL USERS.ID BERDASARKAN USERNAME SEBENARNYA
            |--------------------------------------------------------------------------
            */
            $usernames = [];

            foreach ($rows as $data) {
                $nis = trim((string) ($data['nis'] ?? ''));

                if ($nis !== '') {
                    $usernames[] = $nis;
                    $usernames[] = $nis . 'ortu';
                }
            }

            $userMap = [];

            if (!empty($usernames)) {
                $users = $usersTable
                    ->select('id, username, role')
                    ->whereIn('username', array_values(array_unique($usernames)))
                    ->get()
                    ->getResultArray();

                foreach ($users as $user) {
                    $userMap[$user['username']] = $user;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 3. SIAPKAN PARENTS DENGAN users_id AKTUAL
            |--------------------------------------------------------------------------
            */
            $parentsInsert = [];
            $parentRows    = [];

            foreach ($rows as $data) {
                $nis = trim((string) ($data['nis'] ?? ''));

                $ortuUsername = $nis . 'ortu';

                if (!isset($userMap[$ortuUsername])) {
                    throw new \RuntimeException(
                        'User orang tua tidak ditemukan untuk NIS ' . $nis
                    );
                }

                $parentsInsert[] = [
                    'nama_ortu' => $data['nama_ortu'] ?? '',
                    'wa_ortu'   => $data['wa_ortu'] ?? null,
                    'users_id'  => $userMap[$ortuUsername]['id'],
                ];

                $parentRows[] = [
                    'nis'        => $nis,
                    'users_id'   => $userMap[$ortuUsername]['id'],
                ];
            }

            if (!empty($parentsInsert)) {
                $parentsTable->insertBatch($parentsInsert);
            }

            /*
            |--------------------------------------------------------------------------
            | 4. AMBIL parents.id AKTUAL BERDASARKAN users_id
            |--------------------------------------------------------------------------
            */
            $parentUserIds = array_values(
                array_unique(
                    array_column($parentsInsert, 'users_id')
                )
            );

            $parentMap = [];

            if (!empty($parentUserIds)) {
                $parents = $parentsTable
                    ->select('id, users_id')
                    ->whereIn('users_id', $parentUserIds)
                    ->get()
                    ->getResultArray();

                foreach ($parents as $parent) {
                    $parentMap[(string) $parent['users_id']] = $parent;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 5. SIAPKAN DATA SISWA DENGAN ID AKTUAL
            |--------------------------------------------------------------------------
            */
            $studentsInsert = [];

            foreach ($rows as $data) {
                $nis = trim((string) ($data['nis'] ?? ''));

                $siswaUsername = $nis;
                $ortuUsername  = $nis . 'ortu';

                if (!isset($userMap[$siswaUsername])) {
                    throw new \RuntimeException(
                        'User siswa tidak ditemukan untuk NIS ' . $nis
                    );
                }

                $ortuUserId = $userMap[$ortuUsername]['id'];

                if (!isset($parentMap[(string) $ortuUserId])) {
                    throw new \RuntimeException(
                        'Parent tidak ditemukan untuk NIS ' . $nis
                    );
                }

                $studentsInsert[] = [
                    'nis'              => $nis,
                    'nama_siswa'       => $data['nama_siswa'] ?? '',
                    'wa_siswa'         => $data['wa_siswa'] ?? null,
                    'tahun_masuk'      => $data['tahun_masuk'] ?? null,
                    'users_id'         => $userMap[$siswaUsername]['id'],
                    'ortu_id'          => $parentMap[(string) $ortuUserId]['id'],
                    'status_siswa_id'  => 1,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | 6. INSERT SISWA
            |--------------------------------------------------------------------------
            */
            if (!empty($studentsInsert)) {
                $siswaTable->insertBatch($studentsInsert);
            }

            $this->db->transCommit();

            return true;

        } catch (\Throwable $e) {
            $this->db->transRollback();

            log_message(
                'error',
                'Bulk upload error: ' . $e->getMessage()
            );

            return false;
        }
    }

}