<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;

class StudentController extends BaseController
{
    protected $attendanceModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
    }

    public function dashboard()
    {

        if ($response = $this->requireRole('siswa')) {
            return $response;
        }


        $session = session();
        $userId  = $session->get('user_id');

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'siswa') {
            return redirect()->to('/auth/login');
        }

        // Ambil data siswa berdasarkan akun login dan siswa_akademik aktif
        $db = \Config\Database::connect();

        $student = $db->table('siswa_akademik sa')
            ->select('
                sa.id as siswa_akademik_id,
                sa.siswa_id,
                s.nama_siswa,
                k.nama_kelas,
                j.nama_jurusan
            ')
            ->join('siswa s', 's.id = sa.siswa_id')
            ->join('kelas k', 'k.id = sa.kelas_id', 'left')
            ->join('jurusan j', 'j.id = k.jurusan_id', 'left')
            ->join('tapel t', 't.id = sa.tapel_id')
            ->where('t.aktif', 1)
            ->where('sa.status_akademik_id', 1)
            ->where('s.users_id', $userId)
            ->orderBy('sa.id', 'DESC')
            ->get()
            ->getRowArray();

        if (!$student) {
            return redirect()->to('/auth/login');
        }

        /*
        |--------------------------------------------------------------------------
        | SISWA AKADEMIK AKTIF
        |--------------------------------------------------------------------------
        | Attendance terhubung ke siswa_akademik.id,
        | bukan plot_kelas.id.
        |--------------------------------------------------------------------------
        */
        $siswaAkademik = $db->table('siswa_akademik')
            ->where('siswa_id', (int) $student['siswa_id'])
            ->where('status_akademik_id', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if (!$siswaAkademik) {
            return redirect()
                ->to('/auth/login')
                ->with('error', 'Data akademik siswa tidak ditemukan.');
        }

        $siswaAkademikId = (int) $siswaAkademik['id'];
        $today            = date('Y-m-d');

        // Jam belajar siswa berdasarkan siswa_akademik
        $jamSekolah = $db->table('siswa_akademik sa')
            ->select('jb.jam_masuk, jb.jam_pulang')
            ->join('jam_belajar jb', 'jb.id = sa.jam_belajar_id', 'left')
            ->where('sa.id', $siswaAkademikId)
            ->get()
            ->getRowArray();

        $jamMasukSekolah  = $jamSekolah['jam_masuk'] ?? '07:15:00';
        $jamPulangSekolah = $jamSekolah['jam_pulang'] ?? '15:45:00';

        // Absensi hari ini KHUSUS siswa yang sedang login
        $absen = $this->attendanceModel
            ->where('siswa_akademik_id', $siswaAkademikId)
            ->where('tanggal', $today)
            ->first();

        $jamAbsenMasuk    = $jamMasukSekolah;
        $jamAbsenPulang   = $jamPulangSekolah;
        $status           = 'belum absen';
        $keteranganMasuk  = 'Belum absen';
        $warnaMasuk       = 'bg-warning';

        if ($absen) {
            $jamAbsenMasuk  = $absen['jam_masuk'] ?? $jamMasukSekolah;
            $jamAbsenPulang = $absen['jam_pulang'] ?? $jamPulangSekolah;
            $status         = $absen['status'];

            if ($jamAbsenMasuk < $jamMasukSekolah) {
                $keteranganMasuk = 'Tepat waktu';
                $warnaMasuk      = 'bg-success';
            } elseif ($jamAbsenMasuk > $jamMasukSekolah) {
                $keteranganMasuk = 'Terlambat';
                $warnaMasuk      = 'bg-warning';
            }
        } else {
            $now = date('H:i');
            if ($now >= '17:00') {
                $status          = 'alpha';
                $keteranganMasuk = 'Alpha';
                $warnaMasuk      = 'bg-warning';
            }
        }

        $absenHariIni = [
            'status'           => $status,
            'jam_masuk'        => $jamAbsenMasuk,
            'jam_pulang'       => $jamAbsenPulang,
            'keterangan_masuk' => $keteranganMasuk,
            'warna_masuk'      => $warnaMasuk
        ];

        // Riwayat absensi KHUSUS siswa yang sedang login
        $riwayatAbsensi = $this->attendanceModel
            ->where('siswa_akademik_id', $siswaAkademikId)
            ->orderBy('tanggal', 'DESC')
            ->limit(10)
            ->findAll();

        // Statistik kehadiran KHUSUS siswa yang sedang login
        $statistik = $this->attendanceModel
            ->select("status, COUNT(*) as jumlah")
            ->where('siswa_akademik_id', $siswaAkademikId)
            ->groupBy('status')
            ->findAll();

        return view('beranda/dashboard_siswa', [
            'student'      => $student,
            'absenHariIni' => $absenHariIni,
            'riwayat'      => $riwayatAbsensi,
            'statistik'    => $statistik
        ]);
    }

    /**
     * Riwayat kehadiran siswa yang sedang login.
     *
     * Data attendance diambil berdasarkan siswa_akademik aktif,
     * bukan berdasarkan plot_kelas dan bukan proses absensi.
     */
    public function riwayatKehadiran()
    {
        if ($response = $this->requireRole('siswa')) {
            return $response;
        }

        $session = session();
        $userId  = $session->get('user_id');

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'siswa') {
            return redirect()->to('/auth/login');
        }

        $db = \Config\Database::connect();

        /*
        |--------------------------------------------------------------------------
        | SISWA AKADEMIK AKTIF
        |--------------------------------------------------------------------------
        | Riwayat selalu mengikuti siswa_akademik aktif milik akun login.
        |--------------------------------------------------------------------------
        */
        $siswaAkademik = $db->table('siswa_akademik sa')
            ->select('
                sa.id AS siswa_akademik_id,
                sa.siswa_id,
                s.nama_siswa,
                s.nis,
                k.nama_kelas,
                j.nama_jurusan
            ')
            ->join('siswa s', 's.id = sa.siswa_id')
            ->join('kelas k', 'k.id = sa.kelas_id', 'left')
            ->join('jurusan j', 'j.id = sa.jurusan_id', 'left')
            ->join('tapel t', 't.id = sa.tapel_id')
            ->where('t.aktif', 1)
            ->where('sa.status_akademik_id', 1)
            ->where('s.users_id', $userId)
            ->orderBy('sa.id', 'DESC')
            ->get()
            ->getRowArray();

        if (!$siswaAkademik) {
            return redirect()
                ->to('/auth/login')
                ->with('error', 'Data akademik siswa tidak ditemukan.');
        }

        $siswaAkademikId = (int) $siswaAkademik['siswa_akademik_id'];

        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN & TAHUN
        |--------------------------------------------------------------------------
        */

        $bulan = (int) ($this->request->getGet('bulan') ?? date('n'));
        $tahun = (int) ($this->request->getGet('tahun') ?? date('Y'));

        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) date('n');
        }

        $tahunSekarang = (int) date('Y');

        if ($tahun < 2000 || $tahun > $tahunSekarang + 1) {
            $tahun = $tahunSekarang;
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage = 10;

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT KEHADIRAN
        |--------------------------------------------------------------------------
        | Hanya attendance milik siswa_akademik aktif.
        |--------------------------------------------------------------------------
        */

        $builder = $this->attendanceModel
            ->where('siswa_akademik_id', $siswaAkademikId)
            ->where(
                'tanggal >=',
                sprintf('%04d-%02d-01', $tahun, $bulan)
            )
            ->where(
                'tanggal <=',
                date(
                    'Y-m-t',
                    strtotime(sprintf('%04d-%02d-01', $tahun, $bulan))
                )
            )
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC');

        $riwayatKehadiran = $builder->paginate($perPage, 'kehadiran');

        /*
        |--------------------------------------------------------------------------
        | REKAP STATUS BULAN TERPILIH
        |--------------------------------------------------------------------------
        */

        $rekapRows = $this->attendanceModel
            ->select('status, COUNT(*) AS jumlah')
            ->where('siswa_akademik_id', $siswaAkademikId)
            ->where(
                'tanggal >=',
                sprintf('%04d-%02d-01', $tahun, $bulan)
            )
            ->where(
                'tanggal <=',
                date(
                    'Y-m-t',
                    strtotime(sprintf('%04d-%02d-01', $tahun, $bulan))
                )
            )
            ->groupBy('status')
            ->findAll();

        $rekap = [
            'hadir' => 0,
            'izin'  => 0,
            'sakit' => 0,
            'alpha' => 0,
        ];

        foreach ($rekapRows as $row) {
            $status = strtolower(trim($row['status'] ?? ''));

            if (array_key_exists($status, $rekap)) {
                $rekap[$status] = (int) $row['jumlah'];
            }
        }

        return view('siswa/riwayat_kehadiran', [
            'title'            => 'Riwayat Kehadiran',
            'student'          => $siswaAkademik,
            'riwayatKehadiran' => $riwayatKehadiran,
            'pager'            => $this->attendanceModel->pager,
            'bulan'            => $bulan,
            'tahun'            => $tahun,
            'rekap'            => $rekap,
        ]);
    }


    /**
     * Riwayat pengajuan surat izin/sakit dari orang tua.
     *
     * Data diambil dari tabel surat berdasarkan:
     * - siswa aktif yang sedang login
     * - orang tua yang terhubung dengan siswa tersebut
     *
     * Bukan data attendance.
     */
    public function riwayatIzin()
    {
        if ($response = $this->requireRole('siswa')) {
            return $response;
        }

        $session = session();
        $userId  = $session->get('user_id');

        if (
            !$session->get('isLoggedIn') ||
            $session->get('role') !== 'siswa'
        ) {
            return redirect()->to('/auth/login');
        }

        $db = \Config\Database::connect();

        /*
        |--------------------------------------------------------------------------
        | SISWA AKADEMIK AKTIF
        |--------------------------------------------------------------------------
        */

        $siswaAkademik = $db->table('siswa_akademik sa')
            ->select('
                sa.id AS siswa_akademik_id,
                sa.siswa_id,
                s.nama_siswa,
                s.nis,
                s.ortu_id,
                k.nama_kelas,
                j.nama_jurusan
            ')
            ->join('siswa s', 's.id = sa.siswa_id')
            ->join('kelas k', 'k.id = sa.kelas_id', 'left')
            ->join('jurusan j', 'j.id = sa.jurusan_id', 'left')
            ->join('tapel t', 't.id = sa.tapel_id')
            ->where('t.aktif', 1)
            ->where('sa.status_akademik_id', 1)
            ->where('s.users_id', $userId)
            ->orderBy('sa.id', 'DESC')
            ->get()
            ->getRowArray();

        if (!$siswaAkademik) {
            return redirect()
                ->to('/auth/login')
                ->with('error', 'Data akademik siswa tidak ditemukan.');
        }

        $siswaId = (int) $siswaAkademik['siswa_id'];
        $ortuId  = (int) ($siswaAkademik['ortu_id'] ?? 0);

        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN & TAHUN
        |--------------------------------------------------------------------------
        */

        $bulan = (int) ($this->request->getGet('bulan') ?? date('n'));
        $tahun = (int) ($this->request->getGet('tahun') ?? date('Y'));

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
        | RIWAYAT PENGAJUAN ORANG TUA
        |--------------------------------------------------------------------------
        |
        | Hanya tabel surat.
        | Tidak menggunakan attendance.
        |
        */

        $suratModel = new \App\Models\SuratModel();

        $perPage = 10;

        $builder = $suratModel
            ->where('siswa_id', $siswaId)
            ->where('ortu_id', $ortuId)
            ->where('tanggal_absensi >=', $tanggalAwal)
            ->where('tanggal_absensi <=', $tanggalAkhir)
            ->orderBy('tanggal_absensi', 'DESC')
            ->orderBy('id', 'DESC');

        $riwayatIzin = $builder->paginate($perPage, 'izin');

        return view('siswa/riwayat_izin', [
            'title'       => 'Riwayat Pengajuan Izin',
            'student'     => $siswaAkademik,
            'riwayatIzin' => $riwayatIzin,
            'pager'       => $suratModel->pager,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
        ]);
    }

}
