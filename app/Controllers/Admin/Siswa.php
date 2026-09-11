<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\JurusanModel;
use App\Models\ParentModel;
use App\Models\UserModel;

class Siswa extends BaseController
{
    protected $siswaModel;
    protected $kelasModel;
    protected $jurusanModel;

    public function __construct()
    {
        $this->siswaModel   = new SiswaModel();
        $this->kelasModel   = new KelasModel();
        $this->jurusanModel = new JurusanModel();
    }

    public function index()
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        // Filter dari query string
        $keyword = trim((string) ($this->request->getGet('keyword') ?? ''));
        $kelas   = $this->request->getGet('kelas');
        $jurusan = $this->request->getGet('jurusan');

        /*
         * Query siswa
         *
         * Data yang ditampilkan:
         * - NIS
         * - Nama siswa
         * - Tahun masuk
         * - Nama orang tua
         */
        $builder = $this->siswaModel
            ->select('
                siswa.*,
                kelas.nama_kelas,
                jurusan.nama_jurusan,
                parents.nama_ortu
            ')
            ->join(
                'kelas',
                'kelas.id = siswa.kelas_id',
                'left'
            )
            ->join(
                'jurusan',
                'jurusan.id = siswa.jurusan_id',
                'left'
            )
            ->join(
                'parents',
                'parents.id = siswa.ortu_id',
                'left'
            );

        // Pencarian NIS atau Nama
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('siswa.nis', $keyword)
                ->orLike('siswa.nama_siswa', $keyword)
                ->groupEnd();
        }

        if ($kelas) {
            $builder->where('kelas.nama_kelas', $kelas);
        }

        if ($jurusan) {
            $builder->where('jurusan.nama_jurusan', $jurusan);
        }

        $data['siswa'] = $builder
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->paginate($perPage);

        $data['pager']   = $this->siswaModel->pager;
        $data['perPage'] = $perPage;

        // Dropdown filter
        $data['title']       = 'Daftar Siswa | Admin Panel Absensi Digital';
        $data['kelasList']   = $this->kelasModel->findAll();
        $data['jurusanList'] = $this->jurusanModel->findAll();

        // Nilai filter
        $data['keyword'] = $keyword;
        $data['kelas']   = $kelas;
        $data['jurusan'] = $jurusan;

        return view('admin/siswa/index', $data);
    }

    public function create()
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        return view('admin/siswa/created');
    }


    public function store()
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $db = db_connect();

        /*
        |--------------------------------------------------------------------------
        | VALIDASI FORM
        |--------------------------------------------------------------------------
        */

        $rules = [
            'nis'         => 'required|is_unique[siswa.nis]|max_length[20]',
            'password'    => 'required|min_length[6]',
            'email'       => 'required|valid_email|max_length[100]',
            'nama_siswa'  => 'required|max_length[100]',
            'wa_siswa'    => 'required|numeric|max_length[20]',
            'tahun_masuk' => 'permit_empty|integer|greater_than_equal_to[2000]',
            'nama_ortu'   => 'required|max_length[100]',
            'wa_ortu'     => 'required|numeric|max_length[20]',
            'email_ortu'  => 'permit_empty|valid_email|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA
        |--------------------------------------------------------------------------
        */

        $nis         = trim((string) $this->request->getPost('nis'));
        $password    = (string) $this->request->getPost('password');
        $email       = trim((string) $this->request->getPost('email'));
        $namaSiswa   = trim((string) $this->request->getPost('nama_siswa'));
        $waSiswa     = trim((string) $this->request->getPost('wa_siswa'));
        $tahunMasuk  = $this->request->getPost('tahun_masuk');

        $namaOrtu    = trim((string) $this->request->getPost('nama_ortu'));
        $waOrtu      = trim((string) $this->request->getPost('wa_ortu'));
        $emailOrtu   = trim((string) $this->request->getPost('email_ortu'));

        /*
        |--------------------------------------------------------------------------
        | USERNAME ORANG TUA
        |--------------------------------------------------------------------------
        |
        | Format baru:
        | 99902ortu
        |
        */

        $ortuUsername = $nis . 'ortu';

        if ($emailOrtu === '') {
            $emailOrtu = $ortuUsername . '@dummy.local';
        }


        /*
        |--------------------------------------------------------------------------
        | CEK USERNAME / EMAIL
        |--------------------------------------------------------------------------
        */

        $usersTable = $db->table('users');

        $existingStudentUser = $usersTable
            ->where('username', $nis)
            ->get()
            ->getRowArray();

        if ($existingStudentUser) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    ['nis' => 'Username siswa dengan NIS tersebut sudah digunakan.']
                );
        }


        $existingParentUser = $usersTable
            ->where('username', $ortuUsername)
            ->get()
            ->getRowArray();

        if ($existingParentUser) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    ['nis' => 'Username orang tua ' . $ortuUsername . ' sudah digunakan.']
                );
        }


        $existingStudentEmail = $usersTable
            ->where('email', $email)
            ->get()
            ->getRowArray();

        if ($existingStudentEmail) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    ['email' => 'Email siswa sudah digunakan.']
                );
        }


        $existingParentEmail = $usersTable
            ->where('email', $emailOrtu)
            ->get()
            ->getRowArray();

        if ($existingParentEmail) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    ['email_ortu' => 'Email orang tua sudah digunakan.']
                );
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
            | 1. BUAT USER SISWA
            |--------------------------------------------------------------------------
            */

            $usersTable->insert([
                'username'   => $nis,
                'email'      => $email,
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'role'       => 'siswa',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $studentUserId = $db->insertID();

            if (!$studentUserId) {
                throw new \RuntimeException(
                    'Gagal membuat akun user siswa.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | 2. BUAT USER ORANG TUA
            |--------------------------------------------------------------------------
            */

            $usersTable->insert([
                'username'   => $ortuUsername,
                'email'      => $emailOrtu,
                'password'   => password_hash('123456', PASSWORD_DEFAULT),
                'role'       => 'ortu',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $parentUserId = $db->insertID();

            if (!$parentUserId) {
                throw new \RuntimeException(
                    'Gagal membuat akun user orang tua.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | 3. INSERT PARENTS
            |--------------------------------------------------------------------------
            */

            $parentsTable = $db->table('parents');

            $parentsTable->insert([
                'nama_ortu' => $namaOrtu,
                'wa_ortu'   => $waOrtu,
                'users_id'  => $parentUserId,
            ]);

            $parentId = $db->insertID();

            if (!$parentId) {
                throw new \RuntimeException(
                    'Gagal membuat data orang tua.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | 4. INSERT SISWA
            |--------------------------------------------------------------------------
            |
            | TIDAK mengisi:
            | - kelas_id
            | - jurusan_id
            |
            | Kelas dan jurusan akan dikelola melalui siswa_akademik.
            |
            */

            $siswaData = [
                'nis'             => $nis,
                'nama_siswa'      => $namaSiswa,
                'wa_siswa'        => $waSiswa,
                'tahun_masuk'     => $tahunMasuk ?: null,
                'users_id'        => $studentUserId,
                'ortu_id'         => $parentId,
                'status_siswa_id' => 1,
                'created_at'      => date('Y-m-d H:i:s'),
            ];

            $siswaTable = $db->table('siswa');

            $siswaTable->insert($siswaData);

            if ($db->affectedRows() <= 0) {
                throw new \RuntimeException(
                    'Gagal menyimpan data siswa.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | 5. COMMIT
            |--------------------------------------------------------------------------
            */

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Transaction gagal.'
                );
            }

            $db->transCommit();

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                'Tambah siswa gagal: ' . $e->getMessage()
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    [
                        'general' =>
                            'Data siswa gagal disimpan: ' .
                            $e->getMessage()
                    ]
                );
        }


        /*
        |--------------------------------------------------------------------------
        | BERHASIL
        |--------------------------------------------------------------------------
        */

        session()->setFlashdata(
            'success',
            'Data siswa berhasil ditambahkan. Akun siswa dan orang tua berhasil dibuat.'
        );

        return redirect()->to('/admin/siswa');
    }


public function edit($id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $db = \Config\Database::connect();

        // ====================================================
        // DATA SISWA
        // ====================================================

        $siswa = $this->siswaModel->find($id);

        if (!$siswa) {
            return redirect()
                ->to('/admin/siswa')
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        $data['siswa'] = $siswa;

        // ====================================================
        // DATA ORANG TUA + USERNAME + EMAIL
        //
        // Relasi:
        // siswa.ortu_id
        //     -> parents.id
        //     -> parents.users_id
        //     -> users.id
        // ====================================================

        $data['ortu'] = null;
        $data['username_ortu'] = '-';
        $data['email_ortu'] = '-';

        if (!empty($siswa['ortu_id'])) {

            $ortu = $db
                ->table('parents p')
                ->select('
                    p.id,
                    p.nama_ortu,
                    p.wa_ortu,
                    p.users_id,
                    u.username,
                    u.email
                ')
                ->join(
                    'users u',
                    'u.id = p.users_id',
                    'left'
                )
                ->where('p.id', (int) $siswa['ortu_id'])
                ->get()
                ->getRowArray();

            if ($ortu) {
                $data['ortu'] = $ortu;
                $data['username_ortu'] = $ortu['username'] ?? '-';
                $data['email_ortu'] = $ortu['email'] ?? '-';
            }
        }

        // ====================================================
        // TIDAK ADA LAGI:
        // - kelasList
        // - jurusanList
        // - ortuList
        //
        // Kelas/Jurusan dikelola melalui siswa_akademik.
        // ====================================================

        return view('admin/siswa/edit', $data);
    }


    public function update($id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        // ====================================================
        // VALIDASI
        // ====================================================

        $rules = [
            'nama_siswa'  => 'required|max_length[100]',
            'wa_siswa'    => 'required|numeric|max_length[20]',
            'tahun_masuk' => 'permit_empty|integer|greater_than_equal_to[2000]',

            'nama_ortu'   => 'required|max_length[100]',
            'wa_ortu'     => 'permit_empty|numeric|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // ====================================================
        // CEK DATA SISWA
        // ====================================================

        $siswa = $this->siswaModel->find($id);

        if (!$siswa) {
            return redirect()
                ->back()
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        // ====================================================
        // DATA SISWA YANG BOLEH DIUPDATE
        //
        // TIDAK MENYENTUH:
        // - nis
        // - users_id
        // - ortu_id
        // - kelas_id
        // - jurusan_id
        // ====================================================

        $siswaData = [
            'nama_siswa'  => trim((string) $this->request->getPost('nama_siswa')),
            'wa_siswa'    => trim((string) $this->request->getPost('wa_siswa')),
            'tahun_masuk' => $this->request->getPost('tahun_masuk') ?: null,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        // ====================================================
        // DATA ORANG TUA
        //
        // HANYA:
        // - nama_ortu
        // - wa_ortu
        //
        // USERNAME + EMAIL READ ONLY
        // TIDAK UPDATE TABEL USERS
        // ====================================================

        $ortuData = [
            'nama_ortu'  => trim((string) $this->request->getPost('nama_ortu')),
            'wa_ortu'    => trim((string) $this->request->getPost('wa_ortu')),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // ====================================================
        // TRANSACTION
        // ====================================================

        $db = \Config\Database::connect();

        $db->transStart();

        try {

            // ------------------------------------------------
            // UPDATE SISWA
            // ------------------------------------------------

            if (!$this->siswaModel->update($id, $siswaData)) {
                throw new \RuntimeException(
                    'Data siswa gagal diperbarui.'
                );
            }

            // ------------------------------------------------
            // UPDATE PARENTS
            // ------------------------------------------------

            if (!empty($siswa['ortu_id'])) {

                $parentsTable = $db->table('parents');

                $parentUpdated = $parentsTable
                    ->where(
                        'id',
                        (int) $siswa['ortu_id']
                    )
                    ->update($ortuData);

                if ($parentUpdated === false) {
                    throw new \RuntimeException(
                        'Data orang tua gagal diperbarui.'
                    );
                }
            }

            // ------------------------------------------------
            // CEK TRANSACTION
            // ------------------------------------------------

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Transaction gagal.'
                );
            }

            $db->transComplete();

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                'Update siswa gagal: ' . $e->getMessage()
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    [
                        'general' =>
                            'Data siswa gagal diperbarui: ' .
                            $e->getMessage()
                    ]
                );
        }

        // ====================================================
        // BERHASIL
        // ====================================================

        session()->setFlashdata(
            'success',
            'Data siswa dan data orang tua berhasil diperbarui.'
        );

        return redirect()->to('/admin/siswa');
    }


    public function delete($id)
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        if ($this->siswaModel->delete($id)) {
            session()->setFlashdata('success', 'Data siswa berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data siswa.');
        }
        return redirect()->to('admin/siswa');
    }
}
