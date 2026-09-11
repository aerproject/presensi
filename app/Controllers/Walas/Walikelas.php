<?php
namespace App\Controllers\Walas;

use App\Controllers\BaseController;
use App\Models\SiswaAkademikModel;
use App\Models\TapelModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\WaliKelasModel;
use App\Models\JurusanModel;
use App\Models\AttendanceModel;
use App\Models\SuratModel;
use App\Models\MessageModel;

class Walikelas extends BaseController
{
    public function dashboard()
    {

        if ($response = $this->requireRole('walikelas')) {
            return $response;
        }


        $session = session();

        $guruModel = new \App\Models\GuruModel();

        $guru = $guruModel
            ->where('nama_guru', 'Ahmad Sahidi')
            ->first();


        $walikelasModel  = new WaliKelasModel();
        $kelasModel      = new KelasModel();
        $siswaAkademikModel = new SiswaAkademikModel();
        $jurusanModel    = new JurusanModel();
        $attendanceModel = new AttendanceModel();

        // Cari wali kelas berdasarkan relasi guru
        $guruModel = new \App\Models\GuruModel();

        $guru = $guruModel
            ->where('nama_guru', 'Ahmad Sahidi')
            ->first();

        $walikelas = null;

        if ($guru) {
            $walikelas = $walikelasModel
                ->select("
                    walikelas.*,
                    guru.nama_guru as nama_walas,
                    guru.nip as nohp_walas
                ")
                ->join(
                    'guru',
                    'guru.id = walikelas.guru_id'
                )
                ->where(
                    'walikelas.guru_id',
                    $guru['id']
                )
                ->first();
        }

        $kelas   = null;
        $jurusan = null;
        $siswa   = [];

        if ($walikelas) {
            $kelas = $kelasModel->where('id', $walikelas['kelas_id'])->first();
            if ($kelas) {
                $jurusan = $jurusanModel->find($kelas['jurusan_id']);
                $siswa = $siswaAkademikModel
                    ->select('
                        siswa_akademik.*,
                        siswa.nama_siswa,
                        siswa.nis
                    ')
                    ->join(
                        'siswa',
                        'siswa.id = siswa_akademik.siswa_id'
                    )
                    ->where(
                        'siswa_akademik.kelas_id',
                        $kelas['id']
                    )
                    ->where(
                        'siswa_akademik.tapel_id',
                        $walikelas['tapel_id']
                    )
                    ->findAll();
            }
        }

        // Ranking siswa berdasarkan status absensi
$alphaTop = $attendanceModel
    ->select("
        siswa.nama_siswa,
        COUNT(attendance.id) as total
    ")
    ->join(
        'siswa_akademik',
        'siswa_akademik.id = attendance.siswa_akademik_id'
    )
    ->join(
        'siswa',
        'siswa.id = siswa_akademik.siswa_id'
    )
    ->where(
        'siswa_akademik.kelas_id',
        $kelas['id']
    )
    ->where(
        'attendance.status',
        'alpha'
    )
    ->groupBy(
        'attendance.siswa_akademik_id'
    )
    ->orderBy(
        'total',
        'DESC'
    )
    ->limit(5)
    ->findAll();


$izinTop = $attendanceModel
    ->select("
        siswa.nama_siswa,
        COUNT(attendance.id) as total
    ")
    ->join(
        'siswa_akademik',
        'siswa_akademik.id = attendance.siswa_akademik_id'
    )
    ->join(
        'siswa',
        'siswa.id = siswa_akademik.siswa_id'
    )
    ->where(
        'siswa_akademik.kelas_id',
        $kelas['id']
    )
    ->where(
        'attendance.status',
        'izin'
    )
    ->groupBy(
        'attendance.siswa_akademik_id'
    )
    ->orderBy(
        'total',
        'DESC'
    )
    ->limit(5)
    ->findAll();


$sakitTop = $attendanceModel
    ->select("
        siswa.nama_siswa,
        COUNT(attendance.id) as total
    ")
    ->join(
        'siswa_akademik',
        'siswa_akademik.id = attendance.siswa_akademik_id'
    )
    ->join(
        'siswa',
        'siswa.id = siswa_akademik.siswa_id'
    )
    ->where(
        'siswa_akademik.kelas_id',
        $kelas['id']
    )
    ->where(
        'attendance.status',
        'sakit'
    )
    ->groupBy(
        'attendance.siswa_akademik_id'
    )
    ->orderBy(
        'total',
        'DESC'
    )
    ->limit(5)
    ->findAll();

$data = [
            'title'     => 'Dashboard Wali Kelas',
            'walikelas' => $walikelas,
            'kelas'     => $kelas,
            'jurusan'   => $jurusan,
            'siswa'     => $siswa,
            'alphaTop'  => $alphaTop,
            'izinTop'   => $izinTop,
            'sakitTop'  => $sakitTop
        ];

        return view('walas/dashboard', $data);
    }

    public function siswa()
    {
        if ($response = $this->requireRole('walikelas')) {
            return $response;
        }

        $guruModel          = new \App\Models\GuruModel();
        $walikelasModel     = new WaliKelasModel();
        $kelasModel         = new KelasModel();
        $siswaAkademikModel = new SiswaAkademikModel();
        $tapelModel         = new TapelModel();

        // =====================================================
        // GURU / WALI KELAS
        // =====================================================
        $guru = $guruModel
            ->where('nama_guru', 'Ahmad Sahidi')
            ->first();

        $walikelas = null;

        if ($guru) {
            $walikelas = $walikelasModel
                ->select("
                    walikelas.*,
                    guru.nama_guru AS nama_walas,
                    guru.nip AS nohp_walas
                ")
                ->join(
                    'guru',
                    'guru.id = walikelas.guru_id'
                )
                ->where(
                    'walikelas.guru_id',
                    $guru['id']
                )
                ->first();
        }

        $kelas = null;
        $siswa = [];

        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        // =====================================================
        // TAPEL AKTIF
        // =====================================================
        $tapel = $tapelModel
            ->where('aktif', 1)
            ->first();

        if ($walikelas && $tapel) {

            $kelas = $kelasModel
                ->where('id', $walikelas['kelas_id'])
                ->first();

            if ($kelas) {

                // =================================================
                // SISWA AKTIF KELAS BINAAN
                // Semua relasi kelas melalui siswa_akademik
                // =================================================
                $siswa = $siswaAkademikModel
                    ->select("
                        siswa_akademik.id AS siswa_akademik_id,
                        siswa_akademik.siswa_id,
                        siswa_akademik.kelas_id,
                        siswa_akademik.jurusan_id,
                        siswa_akademik.tapel_id,
                        siswa.nis,
                        siswa.nama_siswa,
                        kelas.nama_kelas,
                        jurusan.nama_jurusan,
                        jam_belajar.shift,
                        jam_belajar.jam_masuk,
                        jam_belajar.jam_pulang
                    ")
                    ->join(
                        'siswa',
                        'siswa.id = siswa_akademik.siswa_id'
                    )
                    ->join(
                        'kelas',
                        'kelas.id = siswa_akademik.kelas_id'
                    )
                    ->join(
                        'jurusan',
                        'jurusan.id = siswa_akademik.jurusan_id',
                        'left'
                    )
                    ->join(
                        'jam_belajar',
                        'jam_belajar.id = siswa_akademik.jam_belajar_id',
                        'left'
                    )
                    ->where(
                        'siswa_akademik.kelas_id',
                        $kelas['id']
                    )
                    ->where(
                        'siswa_akademik.tapel_id',
                        $tapel['id']
                    )
                    ->where(
                        'siswa_akademik.status_akademik_id',
                        1
                    )
                    ->orderBy(
                        'siswa.nama_siswa',
                        'ASC'
                    )
                    ->paginate($perPage, 'siswa');
            }
        }

        $data = [
            'title'     => 'Data Siswa Kelas Binaan',
            'walikelas' => $walikelas,
            'kelas'     => $kelas,
            'siswa'     => $siswa,
            'pager'     => $siswaAkademikModel->pager,
            'perPage'   => $perPage,
            'tapel'     => $tapel ?? null
        ];

        return view('walas/siswa_view', $data);
    }


    public function kehadiran()
    {
        if ($response = $this->requireRole('walikelas')) {
            return $response;
        }

        $guruModel          = new \App\Models\GuruModel();
        $walikelasModel     = new WaliKelasModel();
        $kelasModel         = new KelasModel();
        $siswaAkademikModel = new SiswaAkademikModel();
        $attendanceModel    = new AttendanceModel();
        $tapelModel         = new TapelModel();

        // =====================================================
        // GURU / WALI KELAS
        // =====================================================
        $guru = $guruModel
            ->where('nama_guru', 'Ahmad Sahidi')
            ->first();

        $walikelas = null;

        if ($guru) {
            $walikelas = $walikelasModel
                ->select("
                    walikelas.*,
                    guru.nama_guru AS nama_walas,
                    guru.nip AS nohp_walas
                ")
                ->join(
                    'guru',
                    'guru.id = walikelas.guru_id'
                )
                ->where(
                    'walikelas.guru_id',
                    $guru['id']
                )
                ->first();
        }

        $kelas      = null;
        $siswa      = [];
        $attendance = [];

        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        // =====================================================
        // TAPEL AKTIF
        // =====================================================
        $tapel = $tapelModel
            ->where('aktif', 1)
            ->first();

        if ($walikelas && $tapel) {

            $kelas = $kelasModel
                ->where(
                    'id',
                    $walikelas['kelas_id']
                )
                ->first();

            if ($kelas) {

                // =================================================
                // SISWA AKTIF
                // =================================================
                $siswa = $siswaAkademikModel
                    ->select("
                        siswa_akademik.id AS siswa_akademik_id,
                        siswa.id AS siswa_id,
                        siswa.nis,
                        siswa.nama_siswa,
                        siswa_akademik.kelas_id,
                        siswa_akademik.tapel_id
                    ")
                    ->join(
                        'siswa',
                        'siswa.id = siswa_akademik.siswa_id'
                    )
                    ->where(
                        'siswa_akademik.kelas_id',
                        $kelas['id']
                    )
                    ->where(
                        'siswa_akademik.tapel_id',
                        $tapel['id']
                    )
                    ->where(
                        'siswa_akademik.status_akademik_id',
                        1
                    )
                    ->orderBy(
                        'siswa.nama_siswa',
                        'ASC'
                    )
                    ->findAll();

                // =================================================
                // DATA KEHADIRAN
                //
                // attendance
                //    ↓ siswa_akademik_id
                // siswa_akademik
                //    ↓ siswa_id
                // siswa
                // =================================================
                $attendance = $attendanceModel
                    ->select("
                        attendance.*,
                        siswa_akademik.kelas_id,
                        siswa_akademik.tapel_id,
                        siswa.nis,
                        siswa.nama_siswa
                    ")
                    ->join(
                        'siswa_akademik',
                        'siswa_akademik.id = attendance.siswa_akademik_id'
                    )
                    ->join(
                        'siswa',
                        'siswa.id = siswa_akademik.siswa_id'
                    )
                    ->where(
                        'siswa_akademik.kelas_id',
                        $kelas['id']
                    )
                    ->where(
                        'siswa_akademik.tapel_id',
                        $tapel['id']
                    )
                    ->where(
                        'siswa_akademik.status_akademik_id',
                        1
                    )
                    ->orderBy(
                        'attendance.tanggal',
                        'DESC'
                    )
                    ->paginate(
                        $perPage,
                        'attendance'
                    );
            }
        }

        $data = [
            'title'      => 'Data Kehadiran Siswa',
            'walikelas'  => $walikelas,
            'kelas'      => $kelas,
            'siswa'      => $siswa,
            'attendance' => $attendance,
            'pager'      => $attendanceModel->pager,
            'perPage'    => $perPage,
            'tapel'      => $tapel ?? null
        ];

        return view(
            'walas/kehadiran_view',
            $data
        );
    }


    public function izin()
    {

        if ($response = $this->requireRole('walikelas')) {
            return $response;
        }


        $session = session();

        $guruModel = new \App\Models\GuruModel();

        $guru = $guruModel
            ->where('nama_guru', 'Ahmad Sahidi')
            ->first();


        $walikelasModel = new WaliKelasModel();
        $kelasModel     = new KelasModel();
        $studentModel   = new SiswaModel();
        $suratModel     = new SuratModel();

        // Cari wali kelas berdasarkan relasi guru
        $guruModel = new \App\Models\GuruModel();

        $guru = $guruModel
            ->where('nama_guru', 'Ahmad Sahidi')
            ->first();

        $walikelas = null;

        if ($guru) {
            $walikelas = $walikelasModel
                ->select("
                    walikelas.*,
                    guru.nama_guru as nama_walas,
                    guru.nip as nohp_walas
                ")
                ->join(
                    'guru',
                    'guru.id = walikelas.guru_id'
                )
                ->where(
                    'walikelas.guru_id',
                    $guru['id']
                )
                ->first();
        }

        $kelas = null;
        $siswa = [];
        $izin  = [];

        // ambil perPage dari query string, default 10
        $perPage = $this->request->getGet('perPage') ?? 10;

        if ($walikelas) {
            $kelas = $kelasModel->where('id', $walikelas['kelas_id'])->first();
            if ($kelas) {
                // Ambil hanya siswa yang benar-benar diplot
                // ke kelas dan tapel wali kelas.
                $siswaAkademikModel = new SiswaAkademikModel();

                $siswa = $siswaAkademikModel
                    ->select('
                        siswa_akademik.*,
                        siswa.nis,
                        siswa.nama_siswa,
                        kelas.nama_kelas
                    ')
                    ->join(
                        'siswa',
                        'siswa.id = siswa_akademik.siswa_id'
                    )
                    ->join(
                        'kelas',
                        'kelas.id = siswa_akademik.kelas_id'
                    )
                    ->where(
                        'siswa_akademik.kelas_id',
                        $walikelas['kelas_id']
                    )
                    ->where(
                        'siswa_akademik.tapel_id',
                        $walikelas['tapel_id']
                    )
                    ->where(
                        'siswa_akademik.status_akademik_id',
                        1
                    )
                    ->orderBy(
                        'siswa.nama_siswa',
                        'ASC'
                    )
                    ->findAll();

                // Ambil data surat izin/sakit dengan paginate
                $izin = $suratModel
                    ->select('surat.*, siswa.nis, siswa.nama_siswa')
                    ->join('siswa', 'siswa.id = surat.siswa_id')
                    ->join(
                        'siswa_akademik',
                        'siswa_akademik.siswa_id = siswa.id'
                    )
                    ->where(
                        'siswa_akademik.kelas_id',
                        $kelas['id']
                    )
                    ->where(
                        'siswa_akademik.tapel_id',
                        $walikelas['tapel_id']
                    )
                    ->where(
                        'siswa_akademik.status_akademik_id',
                        1
                    )
                    ->orderBy('surat.created_at', 'DESC')
                    ->paginate($perPage, 'izin');
            }
        }

        $data = [
            'title'     => 'Data Izin Siswa',
            'walikelas' => $walikelas,
            'kelas'     => $kelas,
            'siswa'     => $siswa,
            'izin'      => $izin,
            'pager'     => $suratModel->pager,
            'perPage'   => $perPage
        ];

        return view('walas/izin_view', $data);
    }


    public function pesan()
    {

        if ($response = $this->requireRole('walikelas')) {
            return $response;
        }


        $session = session();

        $guruModel = new \App\Models\GuruModel();

        $guru = $guruModel
            ->where('nama_guru', 'Ahmad Sahidi')
            ->first();


        $walikelasModel = new WaliKelasModel();
        $kelasModel     = new KelasModel();
        $studentModel   = new SiswaModel();
        $messageModel   = new MessageModel();

        // Cari wali kelas berdasarkan relasi guru
        $guruModel = new \App\Models\GuruModel();

        $guru = $guruModel
            ->where('nama_guru', 'Ahmad Sahidi')
            ->first();

        $walikelas = null;

        if ($guru) {
            $walikelas = $walikelasModel
                ->select("
                    walikelas.*,
                    guru.nama_guru as nama_walas,
                    guru.nip as nohp_walas
                ")
                ->join(
                    'guru',
                    'guru.id = walikelas.guru_id'
                )
                ->where(
                    'walikelas.guru_id',
                    $guru['id']
                )
                ->first();
        }

        $kelas = null;
        $siswa = [];
        $messages = [];

        // ambil perPage dari query string, default 10
        $perPage = $this->request->getGet('perPage') ?? 10;

        if ($walikelas) {
            $kelas = $kelasModel->where('id', $walikelas['kelas_id'])->first();
            if ($kelas) {
                // Ambil hanya siswa yang benar-benar diplot
                // ke kelas dan tapel wali kelas.
                $siswaAkademikModel = new SiswaAkademikModel();

                $siswa = $siswaAkademikModel
                    ->select('
                        siswa_akademik.*,
                        siswa.nis,
                        siswa.nama_siswa,
                        kelas.nama_kelas
                    ')
                    ->join(
                        'siswa',
                        'siswa.id = siswa_akademik.siswa_id'
                    )
                    ->join(
                        'kelas',
                        'kelas.id = siswa_akademik.kelas_id'
                    )
                    ->where(
                        'siswa_akademik.kelas_id',
                        $walikelas['kelas_id']
                    )
                    ->where(
                        'siswa_akademik.tapel_id',
                        $walikelas['tapel_id']
                    )
                    ->where(
                        'siswa_akademik.status_akademik_id',
                        1
                    )
                    ->orderBy(
                        'siswa.nama_siswa',
                        'ASC'
                    )
                    ->findAll();

                // Ambil pesan absensi dengan paginate
                $messages = $messageModel
                    ->select('messages.*, siswa.nis, siswa.nama_siswa')
                    ->join('siswa', 'siswa.users_id = messages.users_id')
                    ->join(
                        'siswa_akademik',
                        'siswa_akademik.siswa_id = siswa.id',
                        'inner'
                    )
                    ->where('siswa_akademik.kelas_id', $kelas['id'])
                    ->where('siswa_akademik.status_akademik_id', 1)
                    ->orderBy('messages.waktu_kirim', 'DESC')
                    ->paginate($perPage, 'messages');
            }
        }

        $data = [
            'title'     => 'Notifikasi Absensi',
            'walikelas' => $walikelas,
            'kelas'     => $kelas,
            'siswa'     => $siswa,
            'messages'  => $messages,
            'pager'     => $messageModel->pager,
            'perPage'   => $perPage
        ];

        return view('walas/pesan_view', $data);
    }

}
