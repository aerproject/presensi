<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\UserModel;

class Guru extends BaseController
{
    protected $guruModel;
    protected $userModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data['title'] = 'Guru | Admin Panel Absensi Digital';

        // Ambil nilai perPage dari query string, default 10
        $perPage = (int)($this->request->getGet('perPage') ?? 10);

        // Gunakan paginate agar bisa pakai pager
        $guru = $this->guruModel
            ->orderBy('id', 'ASC')
            ->paginate($perPage, 'default'); // gunakan grup 'default'

        $data['guru']    = $guru;
        $data['pager']   = $this->guruModel->pager;
        $data['perPage'] = $perPage;

        return view('admin/guru/index', $data);
    }


    public function create()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $db = \Config\Database::connect();

        /*
        |--------------------------------------------------------------------------
        | TAPEL AKTIF
        |--------------------------------------------------------------------------
        */
        $tapelAktif = $db->table('tapel')
            ->where('aktif', 1)
            ->get()
            ->getRowArray();

        /*
        |--------------------------------------------------------------------------
        | KELAS AKTIF DARI SISWA_AKADEMIK
        |--------------------------------------------------------------------------
        */
        $kelasAktif = [];

        if ($tapelAktif) {
            $kelasAktif = $db->table('siswa_akademik sa')
                ->select('sa.kelas_id, k.nama_kelas')
                ->join('kelas k', 'k.id = sa.kelas_id', 'inner')
                ->where('sa.tapel_id', $tapelAktif['id'])
                ->where('sa.status_akademik_id', 1)
                ->groupBy('sa.kelas_id, k.nama_kelas')
                ->orderBy('k.nama_kelas', 'ASC')
                ->get()
                ->getResultArray();
        }

        return view('admin/guru/created', [
            'tapelAktif' => $tapelAktif,
            'kelasAktif' => $kelasAktif,
        ]);
    }

    public function store()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $namaGuru = trim((string) $this->request->getPost('nama_guru'));
        $nip      = trim((string) $this->request->getPost('nip'));

        $buatAkun       = $this->request->getPost('buat_akun') === '1';
        $jadikanWalikelas = $this->request->getPost('jadikan_walikelas') === '1';

        $username = trim((string) $this->request->getPost('username'));
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $confirm  = (string) $this->request->getPost('password_confirm');

        $kelasId = (int) $this->request->getPost('kelas_id');

        /*
        |--------------------------------------------------------------------------
        | WALIKELAS WAJIB MEMILIKI AKUN
        |--------------------------------------------------------------------------
        */
        if ($jadikanWalikelas) {
            $buatAkun = true;
        }

        $errors = [];

        if ($namaGuru === '' || mb_strlen($namaGuru) > 100) {
            $errors['nama_guru'] = 'Nama guru wajib diisi maksimal 100 karakter.';
        }

        if ($nip !== '') {
            $existingNip = $this->guruModel
                ->where('nip', $nip)
                ->where('deleted_at IS NULL', null, false)
                ->first();

            if ($existingNip) {
                $errors['nip'] = 'NIP sudah digunakan.';
            }

            if (mb_strlen($nip) > 30) {
                $errors['nip'] = 'NIP maksimal 30 karakter.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI AKUN
        |--------------------------------------------------------------------------
        */
        if ($buatAkun) {

            /*
            |--------------------------------------------------------------------------
            | AKUN GURU
            |--------------------------------------------------------------------------
            | Saat ini users.role tidak memiliki role "guru".
            | Akun guru aplikasi menggunakan role "walikelas".
            | Karena itu akun hanya boleh dibuat bersamaan
            | dengan penugasan sebagai wali kelas.
            |--------------------------------------------------------------------------
            */

            if (!$jadikanWalikelas) {
                $errors['buat_akun'] =
                    'Akun login guru hanya dapat dibuat jika guru dijadikan wali kelas.';
            }

            if (
                $username === '' ||
                !preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)
            ) {
                $errors['username'] =
                    'Username 3–50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
            }

            if ($username !== '' &&
                $this->userModel->where('username', $username)->countAllResults() > 0
            ) {
                $errors['username'] = 'Username sudah digunakan.';
            }

            if (
                $email === '' ||
                !filter_var($email, FILTER_VALIDATE_EMAIL)
            ) {
                $errors['email'] = 'Email tidak valid.';
            }

            if (
                $email !== '' &&
                $this->userModel->where('email', $email)->countAllResults() > 0
            ) {
                $errors['email'] = 'Email sudah digunakan.';
            }

            if (strlen($password) < 6) {
                $errors['password'] = 'Password minimal 6 karakter.';
            }

            if ($password !== $confirm) {
                $errors['password_confirm'] =
                    'Konfirmasi password tidak sama.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI WALIKELAS
        |--------------------------------------------------------------------------
        */
        $tapelAktif = null;

        if ($jadikanWalikelas) {

            $db = \Config\Database::connect();

            $tapelAktif = $db->table('tapel')
                ->where('aktif', 1)
                ->get()
                ->getRowArray();

            if (!$tapelAktif) {
                $errors['kelas_id'] =
                    'Tidak ada tahun pelajaran aktif.';
            }

            if ($kelasId <= 0) {
                $errors['kelas_id'] =
                    'Kelas wali kelas wajib dipilih.';
            }

            /*
            | Pastikan kelas berasal dari siswa_akademik aktif.
            */
            if ($tapelAktif && $kelasId > 0) {

                $kelasValid = $db->table('siswa_akademik')
                    ->where('tapel_id', $tapelAktif['id'])
                    ->where('kelas_id', $kelasId)
                    ->where('status_akademik_id', 1)
                    ->countAllResults();

                if ($kelasValid === 0) {
                    $errors['kelas_id'] =
                        'Kelas tidak memiliki siswa aktif pada tahun pelajaran aktif.';
                }

                /*
                | Cegah dua wali kelas pada kelas + tapel yang sama.
                */
                $waliExists = $db->table('walikelas')
                    ->where('kelas_id', $kelasId)
                    ->where('tapel_id', $tapelAktif['id'])
                    ->where('deleted_at IS NULL', null, false)
                    ->countAllResults();

                if ($waliExists > 0) {
                    $errors['kelas_id'] =
                        'Kelas tersebut sudah memiliki wali kelas.';
                }
            }
        }

        if (!empty($errors)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $errors);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */
        $db = \Config\Database::connect();

        $db->transBegin();

        try {

            /*
            |--------------------------------------------------------------------------
            | INSERT GURU
            |--------------------------------------------------------------------------
            */
            $guruId = $this->guruModel->insert([
                'nama_guru' => $namaGuru,
                'nip'       => $nip !== '' ? $nip : null,
            ], true);

            if (!$guruId) {
                throw new \RuntimeException(
                    'Gagal menyimpan data guru.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | BUAT AKUN
            |--------------------------------------------------------------------------
            */
            $userId = null;

            if ($buatAkun) {

                $userId = $this->userModel->insert([
                    'username' => $username,
                    'email'    => $email,
                    'password' => password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    ),
                    'role'     => 'walikelas',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ], true);

                if (!$userId) {
                    throw new \RuntimeException(
                        'Gagal membuat akun login guru.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | HUBUNGKAN GURU ↔ USERS
                |--------------------------------------------------------------------------
                */
                if (!$this->guruModel->update($guruId, [
                    'users_id' => $userId,
                ])) {
                    throw new \RuntimeException(
                        'Gagal menghubungkan guru dengan akun login.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | BUAT PENUGASAN WALI KELAS
            |--------------------------------------------------------------------------
            */
            if ($jadikanWalikelas) {

                $waliId = $db->table('walikelas')->insert([
                    'guru_id'     => $guruId,
                    'kelas_id'    => $kelasId,
                    'tapel_id'    => $tapelAktif['id'],
                    'skema_absen' => $this->request->getPost('skema_absen') ?: 'full_day',
                    'sesi'        => $this->request->getPost('sesi') ?: 'pagi',
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);

                if (!$waliId) {
                    throw new \RuntimeException(
                        'Gagal membuat penugasan wali kelas.'
                    );
                }
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Transaksi database gagal.'
                );
            }

            $db->transCommit();

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                'Tambah guru gagal: ' . $e->getMessage()
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    [
                        'database' =>
                            'Gagal menyimpan data guru. Tidak ada data yang diubah.'
                    ]
                );
        }

        $message = 'Data guru berhasil disimpan.';

        if ($jadikanWalikelas) {
            $message .=
                ' Akun wali kelas dan penugasan kelas berhasil dibuat.';
        }

        session()->setFlashdata('success', $message);

        return redirect()->to('/admin/guru');
    }

    public function edit($id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $db = \Config\Database::connect();

        $guru = $this->guruModel->find($id);

        $rawGuruDebug = $db->query(
            'SELECT id, nama_guru, users_id, nip
             FROM guru
             WHERE id = ?
             LIMIT 1',
            [(int) $id]
        )->getRowArray();

        file_put_contents(
            WRITEPATH . 'logs/guru-model-vs-db.log',
            date('Y-m-d H:i:s') .
            ' | ID=' . $id .
            ' | MODEL=' . json_encode($guru) .
            ' | RAW_DB=' . json_encode($rawGuruDebug) .
            PHP_EOL,
            FILE_APPEND
        );

        if (!$guru) {
            return redirect()
                ->to('/admin/guru')
                ->with('error', 'Data guru tidak ditemukan.');
        }

        /*
        |--------------------------------------------------------------------------
        | AKUN LOGIN GURU
        |--------------------------------------------------------------------------
        */
        /*
        |--------------------------------------------------------------------------
        | AKUN LOGIN GURU - SUMBER DATA RAW DATABASE
        |--------------------------------------------------------------------------
        | Jangan pernah menggunakan akun lain sebagai akun guru.
        | Hanya users_id milik record guru ini yang boleh digunakan.
        |--------------------------------------------------------------------------
        */

        $user = null;

        $guruUsersId = $rawGuruDebug['users_id'] ?? null;

        if (
            $guruUsersId !== null &&
            $guruUsersId !== '' &&
            (int) $guruUsersId > 0
        ) {
            $user = $db->table('users')
                ->where('id', (int) $guruUsersId)
                ->get()
                ->getRowArray();
        }

        file_put_contents(
            WRITEPATH . 'logs/guru-edit-user.log',
            date('Y-m-d H:i:s') .
            ' | guru_id=' . (int) $id .
            ' | raw_users_id=' . var_export($guruUsersId, true) .
            ' | final_user=' . json_encode($user) .
            PHP_EOL,
            FILE_APPEND
        );

        /*
        |--------------------------------------------------------------------------
        | TAPEL AKTIF
        |--------------------------------------------------------------------------
        */
        $tapelAktif = $db->table('tapel')
            ->where('aktif', 1)
            ->get()
            ->getRowArray();

        /*
        |--------------------------------------------------------------------------
        | KELAS AKTIF DARI SISWA_AKADEMIK
        |--------------------------------------------------------------------------
        */
        $kelasAktif = [];

        if ($tapelAktif) {
            $kelasAktif = $db->table('siswa_akademik sa')
                ->select('sa.kelas_id, k.nama_kelas')
                ->join('kelas k', 'k.id = sa.kelas_id', 'inner')
                ->where('sa.tapel_id', $tapelAktif['id'])
                ->where('sa.status_akademik_id', 1)
                ->groupBy('sa.kelas_id, k.nama_kelas')
                ->orderBy('k.nama_kelas', 'ASC')
                ->get()
                ->getResultArray();
        }

        /*
        |--------------------------------------------------------------------------
        | PENUGASAN WALI KELAS AKTIF
        |--------------------------------------------------------------------------
        */
        $waliKelas = null;

        if ($tapelAktif) {
            $waliKelas = $db->table('walikelas wk')
                ->select('
                    wk.*,
                    k.nama_kelas,
                    t.tahun_pelajaran,
                    t.semester
                ')
                ->join('kelas k', 'k.id = wk.kelas_id', 'left')
                ->join('tapel t', 't.id = wk.tapel_id', 'left')
                ->where('wk.guru_id', $id)
                ->where('wk.tapel_id', $tapelAktif['id'])
                ->where('wk.deleted_at IS NULL', null, false)
                ->orderBy('wk.id', 'DESC')
                ->get()
                ->getRowArray();
        }

        return view('admin/guru/edit', [
            'guru'       => $guru,
            'user'       => $user,
            'tapelAktif' => $tapelAktif,
            'kelasAktif' => $kelasAktif,
            'waliKelas'  => $waliKelas,
        ]);
    }


    public function update($id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $db = \Config\Database::connect();

        $guru = $this->guruModel->find($id);

        if (!$guru) {
            return redirect()
                ->to('/admin/guru')
                ->with('error', 'Data guru tidak ditemukan.');
        }

        /*
        |--------------------------------------------------------------------------
        | DATA FORM
        |--------------------------------------------------------------------------
        */

        $namaGuru = trim((string) $this->request->getPost('nama_guru'));
        $nip      = trim((string) $this->request->getPost('nip'));

        $jadikanWalikelas =
            $this->request->getPost('jadikan_walikelas') === '1';

        $buatAkun =
            $this->request->getPost('buat_akun') === '1';

        $kelasId =
            (int) $this->request->getPost('kelas_id');

        $username =
            trim((string) $this->request->getPost('username'));

        $email =
            trim((string) $this->request->getPost('email'));

        $password =
            (string) $this->request->getPost('password');

        $confirm =
            (string) $this->request->getPost('password_confirm');

        $errors = [];

        /*
        |--------------------------------------------------------------------------
        | DATA AKUN YANG SUDAH TERHUBUNG
        |--------------------------------------------------------------------------
        */

        $existingUser = null;

        if (!empty($guru['users_id'])) {
            $existingUser = $db->table('users')
                ->where('id', (int) $guru['users_id'])
                ->get()
                ->getRowArray();
        }

        /*
        |--------------------------------------------------------------------------
        | DEBUG FINAL
        |--------------------------------------------------------------------------
        */

        log_message(
            'debug',
            'GURU UPDATE FINAL | guru_id=' . $id .
            ' | users_id=' . var_export($guru['users_id'] ?? null, true) .
            ' | existing_user_id=' . var_export($existingUser['id'] ?? null, true) .
            ' | buat_akun=' . var_export($buatAkun, true) .
            ' | jadikan_walikelas=' . var_export($jadikanWalikelas, true) .
            ' | username=[' . $username . ']'
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DATA GURU
        |--------------------------------------------------------------------------
        */

        if ($namaGuru === '') {
            $errors['nama_guru'] =
                'Nama guru wajib diisi.';
        } elseif (mb_strlen($namaGuru) > 100) {
            $errors['nama_guru'] =
                'Nama guru maksimal 100 karakter.';
        }

        if ($nip !== '') {

            $nipExists = $db->table('guru')
                ->select('id')
                ->where('nip', $nip)
                ->where('id !=', $id)
                ->where('deleted_at IS NULL', null, false)
                ->limit(1)
                ->get()
                ->getRowArray();

            if ($nipExists) {
                $errors['nip'] =
                    'NIP sudah digunakan oleh guru lain.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TAPEL AKTIF
        |--------------------------------------------------------------------------
        */

        $tapelAktif = $db->table('tapel')
            ->where('aktif', 1)
            ->get()
            ->getRowArray();

        /*
        |--------------------------------------------------------------------------
        | WALI KELAS EXISTING
        |--------------------------------------------------------------------------
        */

        $waliExisting = null;

        if ($tapelAktif) {
            $waliExisting = $db->table('walikelas')
                ->where('guru_id', $id)
                ->where('tapel_id', $tapelAktif['id'])
                ->where('deleted_at IS NULL', null, false)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA DIJADIKAN WALI KELAS
        |--------------------------------------------------------------------------
        */

        if ($jadikanWalikelas) {

            $buatAkun = true;

            if (!$tapelAktif) {
                $errors['kelas_id'] =
                    'Tidak ada tahun pelajaran aktif.';
            }

            if ($kelasId <= 0) {
                $errors['kelas_id'] =
                    'Kelas wali kelas wajib dipilih.';
            }

            if ($tapelAktif && $kelasId > 0) {

                $kelasValid = $db->table('siswa_akademik')
                    ->where('tapel_id', $tapelAktif['id'])
                    ->where('kelas_id', $kelasId)
                    ->where('status_akademik_id', 1)
                    ->countAllResults();

                if ($kelasValid === 0) {
                    $errors['kelas_id'] =
                        'Kelas tidak memiliki siswa aktif pada tahun pelajaran aktif.';
                }

                /*
                | Kelas boleh dimiliki guru ini sendiri.
                | Hanya wali guru lain yang dianggap konflik.
                */

                $waliQuery = $db->table('walikelas')
                    ->where('kelas_id', $kelasId)
                    ->where('tapel_id', $tapelAktif['id'])
                    ->where('deleted_at IS NULL', null, false);

                if ($waliExisting) {
                    $waliQuery->where(
                        'id !=',
                        $waliExisting['id']
                    );
                }

                if ($waliQuery->countAllResults() > 0) {
                    $errors['kelas_id'] =
                        'Kelas tersebut sudah memiliki wali kelas.';
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | AKUN BARU
        |--------------------------------------------------------------------------
        |
        | PENTING:
        | Validasi username/email hanya dilakukan jika:
        | - guru BELUM mempunyai users_id
        | - dan akun baru memang akan dibuat.
        |
        */

        if ($buatAkun && $existingUser === null) {

            file_put_contents(
                WRITEPATH . 'logs/guru-flow.log',
                date('Y-m-d H:i:s') .
                ' | BEFORE_NEW_ACCOUNT_VALIDATION' .
                ' | guru_id=' . $id .
                ' | guru_users_id=' . var_export($guru['users_id'] ?? null, true) .
                ' | existingUser=' . json_encode($existingUser) .
                ' | buatAkun=' . var_export($buatAkun, true) .
                ' | username=[' . $username . ']' .
                PHP_EOL,
                FILE_APPEND
            );

            if (!$jadikanWalikelas) {
                $errors['buat_akun'] =
                    'Akun login guru hanya dapat dibuat jika guru dijadikan wali kelas.';
            }

            /*
            | USERNAME
            */

            if (
                $username === '' ||
                !preg_match(
                    '/^[A-Za-z0-9._-]{3,50}$/',
                    $username
                )
            ) {

                $errors['username'] =
                    'Username 3–50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';

            } else {

                $row = $db->query(
                    'SELECT id FROM users WHERE username = ? LIMIT 1',
                    [$username]
                )->getRowArray();

                if ($row !== null) {
                    file_put_contents(
                        WRITEPATH . 'logs/guru-username-source.log',
                        date('Y-m-d H:i:s') .
                        ' | SOURCE=AKUN_BARU_VALIDASI' .
                        ' | guru_id=' . $id .
                        ' | username=[' . $username . ']' .
                        ' | row=' . json_encode($row) .
                        PHP_EOL,
                        FILE_APPEND
                    );

                    $errors['username'] =
                        'Username sudah digunakan.';
                }
            }

            /*
            | EMAIL
            */

            if (
                $email === '' ||
                !filter_var($email, FILTER_VALIDATE_EMAIL)
            ) {

                $errors['email'] =
                    'Email tidak valid.';

            } else {

                $row = $db->query(
                    'SELECT id FROM users WHERE email = ? LIMIT 1',
                    [$email]
                )->getRowArray();

                if ($row !== null) {
                    $errors['email'] =
                        'Email sudah digunakan.';
                }
            }

            /*
            | PASSWORD
            */

            if (strlen($password) < 6) {
                $errors['password'] =
                    'Password minimal 6 karakter.';
            }

            if ($password !== $confirm) {
                $errors['password_confirm'] =
                    'Konfirmasi password tidak sama.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PASSWORD AKUN EXISTING
        |--------------------------------------------------------------------------
        */

        if ($existingUser !== null && $password !== '') {

            if (strlen($password) < 6) {
                $errors['password'] =
                    'Password minimal 6 karakter.';
            }

            if ($password !== $confirm) {
                $errors['password_confirm'] =
                    'Konfirmasi password tidak sama.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STOP JIKA VALIDASI GAGAL
        |--------------------------------------------------------------------------
        */

        if (!empty($errors)) {

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $errors);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $db->transBegin();

        try {

            /*
            |--------------------------------------------------------------------------
            | UPDATE GURU
            |--------------------------------------------------------------------------
            */

            if (!$this->guruModel->update($id, [
                'nama_guru' => $namaGuru,
                'nip'       => $nip !== '' ? $nip : null,
            ])) {
                throw new \RuntimeException(
                    'Gagal memperbarui data guru.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | BUAT AKUN BARU
            |--------------------------------------------------------------------------
            */

            if ($buatAkun && $existingUser === null) {

                /*
                | Double-check langsung ke database.
                | Ini hanya untuk akun BARU.
                */

                $usernameExists = $db->query(
                    'SELECT id FROM users WHERE username = ? LIMIT 1',
                    [$username]
                )->getRowArray();

                if ($usernameExists !== null) {
                    file_put_contents(
                        WRITEPATH . 'logs/guru-username-source.log',
                        date('Y-m-d H:i:s') .
                        ' | SOURCE=AKUN_BARU_DOUBLE_CHECK' .
                        ' | guru_id=' . $id .
                        ' | username=[' . $username . ']' .
                        ' | row=' . json_encode($usernameExists) .
                        PHP_EOL,
                        FILE_APPEND
                    );

                    throw new \RuntimeException(
                        'Username sudah digunakan.'
                    );
                }

                $emailExists = $db->query(
                    'SELECT id FROM users WHERE email = ? LIMIT 1',
                    [$email]
                )->getRowArray();

                if ($emailExists !== null) {
                    throw new \RuntimeException(
                        'Email sudah digunakan.'
                    );
                }

                $userId = $db->table('users')->insert([
                    'username'   => $username,
                    'email'      => $email,
                    'password'   => password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    ),
                    'role'       => 'walikelas',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ], true);

                if (!$userId) {
                    throw new \RuntimeException(
                        'Gagal membuat akun login guru.'
                    );
                }

                if (!$this->guruModel->update($id, [
                    'users_id' => $userId,
                ])) {
                    throw new \RuntimeException(
                        'Gagal menghubungkan guru dengan akun login.'
                    );
                }

            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE AKUN EXISTING
            |--------------------------------------------------------------------------
            */

            if ($existingUser !== null) {

                $userUpdate = [
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                /*
                | Username hanya diubah jika form mengirim username.
                |
                | Username milik user sendiri TIDAK dianggap konflik.
                */

                if ($username !== '') {

                    $usernameConflict = $db->query(
                        'SELECT id
                         FROM users
                         WHERE username = ?
                           AND id != ?
                         LIMIT 1',
                        [
                            $username,
                            (int) $existingUser['id'],
                        ]
                    )->getRowArray();

                    if ($usernameConflict !== null) {
                        file_put_contents(
                            WRITEPATH . 'logs/guru-username-source.log',
                            date('Y-m-d H:i:s') .
                            ' | SOURCE=AKUN_EXISTING_CONFLICT' .
                            ' | guru_id=' . $id .
                            ' | existing_user_id=' . (int) $existingUser['id'] .
                            ' | username=[' . $username . ']' .
                            ' | row=' . json_encode($usernameConflict) .
                            PHP_EOL,
                            FILE_APPEND
                        );

                        throw new \RuntimeException(
                            'Username sudah digunakan.'
                        );
                    }

                    $userUpdate['username'] = $username;
                }

                /*
                | EMAIL
                */

                if ($email !== '') {

                    $emailConflict = $db->query(
                        'SELECT id
                         FROM users
                         WHERE email = ?
                           AND id != ?
                         LIMIT 1',
                        [
                            $email,
                            (int) $existingUser['id'],
                        ]
                    )->getRowArray();

                    if ($emailConflict !== null) {
                        throw new \RuntimeException(
                            'Email sudah digunakan.'
                        );
                    }

                    $userUpdate['email'] = $email;
                }

                /*
                | PASSWORD
                */

                if ($password !== '') {
                    $userUpdate['password'] =
                        password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        );
                }

                if (!$db->table('users')
                    ->where(
                        'id',
                        (int) $existingUser['id']
                    )
                    ->update($userUpdate)
                ) {
                    throw new \RuntimeException(
                        'Gagal memperbarui akun login.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | WALI KELAS
            |--------------------------------------------------------------------------
            */

            if ($jadikanWalikelas) {

                if (!$waliExisting) {

                    $inserted =
                        $db->table('walikelas')->insert([
                            'guru_id'     => $id,
                            'kelas_id'    => $kelasId,
                            'tapel_id'    => $tapelAktif['id'],
                            'skema_absen' => 'full_day',
                            'sesi'        => 'pagi',
                            'created_at'  => date('Y-m-d H:i:s'),
                        ]);

                    if (!$inserted) {
                        throw new \RuntimeException(
                            'Gagal membuat penugasan wali kelas.'
                        );
                    }

                } else {

                    $updated =
                        $db->table('walikelas')
                            ->where(
                                'id',
                                $waliExisting['id']
                            )
                            ->update([
                                'kelas_id'    => $kelasId,
                                'skema_absen' => 'full_day',
                                'sesi'        => 'pagi',
                                'updated_at'  => date('Y-m-d H:i:s'),
                            ]);

                    if (!$updated) {
                        throw new \RuntimeException(
                            'Gagal memperbarui penugasan wali kelas.'
                        );
                    }
                }

            } elseif ($waliExisting) {

                /*
                | Cabut penugasan wali kelas.
                */

                $updated =
                    $db->table('walikelas')
                        ->where(
                            'id',
                            $waliExisting['id']
                        )
                        ->update([
                            'deleted_at' =>
                                date('Y-m-d H:i:s'),
                            'updated_at' =>
                                date('Y-m-d H:i:s'),
                        ]);

                if (!$updated) {
                    throw new \RuntimeException(
                        'Gagal menghapus penugasan wali kelas.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | TRANSACTION STATUS
            |--------------------------------------------------------------------------
            */

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Transaksi database gagal.'
                );
            }

            $db->transCommit();

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                'Update guru gagal | guru_id=' .
                $id .
                ' | ' .
                $e->getMessage()
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    [
                        'general' =>
                            $e->getMessage()
                    ]
                );
        }

        session()->setFlashdata(
            'success',
            'Data guru berhasil diperbarui.'
        );

        return redirect()->to('/admin/guru');
    }


    public function delete($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        if ($this->guruModel->delete($id)) {
            session()->setFlashdata('success', 'Data guru berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data guru.');
        }

        return redirect()->to('/admin/guru');
    }
}
