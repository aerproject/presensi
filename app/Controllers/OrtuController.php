<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\OrtuService;
use App\Models\AttendanceModel;
use App\Models\MessageModel;
use App\Models\ParentModel;
use App\Models\IzinModel;
use App\Models\SuratModel;

class OrtuController extends BaseController
{
    public function dashboard()
    {

        if ($response = $this->requireRole('ortu')) {
            return $response;
        }


        $session = session();
        $userId  = $session->get('user_id');

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'ortu') {
            return redirect()->to('/auth/login');
        }

        $service = new OrtuService($userId);

        $data = $service->getDashboardData($this->request);

        return view('beranda/dashboard_ortu', $data);
    }
    /**
     * Menampilkan riwayat pengajuan izin/surat siswa milik orang tua.
     */
    public function riwayatIzin()
    {
        if ($response = $this->requireRole('ortu')) {
            return $response;
        }

        $session = session();
        $userId  = $session->get('user_id');

        if (
            !$session->get('isLoggedIn') ||
            $session->get('role') !== 'ortu'
        ) {
            return redirect()->to('/auth/login');
        }

        $parentModel = new ParentModel();
        $ortu = $parentModel
            ->where('users_id', $userId)
            ->first();

        if (!$ortu) {
            return redirect()->to('/ortu/dashboard')
                ->with('error', 'Data orang tua tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        /*
        |--------------------------------------------------------------------------
        | ANAK AKTIF
        |--------------------------------------------------------------------------
        */

        $anak = $db->table('siswa s')
            ->select("
                s.id AS siswa_id,
                s.nama_siswa,
                s.nis,
                s.ortu_id,
                sa.id AS siswa_akademik_id,
                k.nama_kelas,
                j.nama_jurusan
            ")
            ->join(
                'siswa_akademik sa',
                'sa.siswa_id = s.id',
                'inner'
            )
            ->join(
                'kelas k',
                'k.id = sa.kelas_id',
                'left'
            )
            ->join(
                'jurusan j',
                'j.id = sa.jurusan_id',
                'left'
            )
            ->join(
                'tapel t',
                't.id = sa.tapel_id',
                'inner'
            )
            ->where('s.ortu_id', $ortu['id'])
            ->where('t.aktif', 1)
            ->orderBy('sa.id', 'DESC')
            ->get()
            ->getRowArray();

        if (!$anak) {
            return redirect()->to('/ortu/dashboard')
                ->with('error', 'Data siswa aktif tidak ditemukan.');
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN & TAHUN
        |--------------------------------------------------------------------------
        */

        $bulan = (int) (
            $this->request->getGet('bulan') ?? date('n')
        );

        $tahun = (int) (
            $this->request->getGet('tahun') ?? date('Y')
        );

        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) date('n');
        }

        $tahunSekarang = (int) date('Y');

        if ($tahun < 2000 || $tahun > $tahunSekarang + 1) {
            $tahun = $tahunSekarang;
        }

        $tanggalAwal = sprintf(
            '%04d-%02d-01',
            $tahun,
            $bulan
        );

        $tanggalAkhir = date(
            'Y-m-t',
            strtotime($tanggalAwal)
        );

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT SURAT IZIN / SAKIT
        |--------------------------------------------------------------------------
        | Hanya milik anak aktif orang tua yang login.
        |--------------------------------------------------------------------------
        */

        $suratModel = new SuratModel();

        $perPage = (int) (
            $this->request->getGet('perPage') ?? 10
        );

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $izinList = $suratModel
            ->where('siswa_id', $anak['siswa_id'])
            ->where(
                'tanggal_absensi >=',
                $tanggalAwal
            )
            ->where(
                'tanggal_absensi <=',
                $tanggalAkhir
            )
            ->orderBy('tanggal_absensi', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($perPage, 'izin');

        return view('beranda/riwayat_izin_ortu', [
            'namaOrtu'    => $ortu['nama_ortu'] ?? 'Orang Tua',
            'namaSiswa'   => $anak['nama_siswa'] ?? '-',
            'namaKelas'   => $anak['nama_kelas'] ?? '-',
            'namaJurusan' => $anak['nama_jurusan'] ?? '-',
            'anak'        => $anak,
            'izinList'    => $izinList,
            'pager'       => $suratModel->pager,
            'perPage'     => $perPage,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
        ]);
    }


    /**
     * Menampilkan pesan untuk orang tua.
     */
    public function pesan()
    {
        if ($response = $this->requireRole('ortu')) {
            return $response;
        }

        $session = session();
        $userId  = $session->get('user_id');

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'ortu') {
            return redirect()->to('/auth/login');
        }

        $service = new OrtuService($userId);

        /*
         * Gunakan data dashboard yang sudah tersedia
         * di OrtuService, termasuk riwayatMessage.
         */
        $data = $service->getDashboardData($this->request);

        return view('beranda/pesan_ortu', [
            'namaOrtu'       => $data['namaOrtu'] ?? 'Orang Tua',
            'namaSiswa'      => $data['namaSiswa'] ?? ($data['anak']['nama_siswa'] ?? '-'),
            'namaKelas'      => $data['namaKelas'] ?? ($data['anak']['nama_kelas'] ?? '-'),
            'namaJurusan'    => $data['namaJurusan'] ?? ($data['anak']['nama_jurusan'] ?? '-'),
            'anak'           => $data['anak'] ?? null,
            'riwayatMessage' => $data['riwayatMessage'] ?? [],
            'pager'          => $data['pagerMessage'] ?? null,
            'perPage'        => $data['perPageMessage'] ?? 5,
        ]);
    }


    public function izin()
    {

        if ($response = $this->requireRole('ortu')) {
            return $response;
        }


        $session = session();
        $userId  = $session->get('user_id');

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'ortu') {
            return redirect()->to('/auth/login');
        }

        $izinModel   = new IzinModel();
        $parentModel = new ParentModel();
        $suratModel  = new SuratModel();

        $ortu = $parentModel->where('users_id', $userId)->first();

        $db = \Config\Database::connect();

        $anak = $db->table('siswa s')
            ->select("
                s.id AS siswa_id,
                s.ortu_id,
                sa.id AS siswa_akademik_id,
                sa.kelas_id,
                sa.jurusan_id,
                sa.tapel_id,
                t.aktif AS tapel_aktif
            ")
            ->join(
                'siswa_akademik sa',
                'sa.siswa_id = s.id',
                'inner'
            )
            ->join(
                'tapel t',
                't.id = sa.tapel_id',
                'inner'
            )
            ->where('s.ortu_id', $ortu['id'])
            ->where('t.aktif', 1)
            ->orderBy('sa.id', 'DESC')
            ->get()
            ->getRowArray();

        if (!$anak) {
            return redirect()->to('/ortu/dashboard')->with('error', 'Data anak tidak ditemukan.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'tanggal'      => 'required|valid_date[Y-m-d]',
            'jenis_izin'   => 'required|in_list[izin,sakit]',
            'isi_pesan'    => 'required|min_length[10]',
            'upload_surat' => 'max_size[upload_surat,2048]|ext_in[upload_surat,pdf,jpg,png]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->to('/izin')->withInput()->with('errors', $validation->getErrors());
        }

        $tanggalAbsensi = $this->request->getPost('tanggal');
        $jenisIzin      = $this->request->getPost('jenis_izin');
        $isiPesan       = $this->request->getPost('isi_pesan');
        $uploadSurat    = $this->request->getFile('upload_surat');

        /*
        |--------------------------------------------------------------------------
        | SIMPAN SURAT PENGAJUAN ORANG TUA
        |--------------------------------------------------------------------------
        |
        | tanggal_absensi = tanggal siswa sebenarnya tidak hadir
        | created_at      = waktu surat masuk ke sistem
        | updated_at      = NULL sampai surat diproses operator
        |
        */

        $now = date('Y-m-d H:i:s');

        $data = [
            'siswa_id'        => $anak['siswa_id'],
            'ortu_id'         => $anak['ortu_id'],
            'jenis'           => $jenisIzin,
            'tanggal_absensi' => $tanggalAbsensi,
            'isi_pesan'       => $isiPesan,
            'status'          => 'diajukan',
            'created_at'      => $now,
            'updated_at'      => null
        ];

        if ($uploadSurat && $uploadSurat->isValid() && !$uploadSurat->hasMoved()) {
            $newName = $uploadSurat->getRandomName();
            $uploadSurat->move(ROOTPATH . 'public/uploads/surat_izin', $newName);
            $data['upload_surat'] = $newName;
        }

        if ($suratModel->insert($data)) {
            return redirect()->to('/ortu/dashboard')->with('success', 'Pengajuan surat berhasil dikirim.');
        } else {
            return redirect()->to('/ortu/dashboard')->with('error', 'Gagal mengirim pengajuan surat.');
        }
    }
}
