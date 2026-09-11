<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\SiswaAkademikModel;

class Dashboard extends BaseController
{
    protected $attendanceModel;
    protected $siswaAkademikModel;

    public function __construct()
    {
        $this->attendanceModel    = new AttendanceModel();
        $this->siswaAkademikModel = new SiswaAkademikModel();
    }

    /*
    =====================================
    DASHBOARD PAGE
    =====================================
    */
    public function index()
    {
        $licenseRuntime = (new \App\Models\LicenseRuntimeModel())
            ->getRuntime();

        return view('admin/dashboard', [
            'title' => 'Dashboard | Admin Panel Absensi Digital',
            'licenseRuntime' => $licenseRuntime,
        ]);
    }

    /*
    =====================================
    CARD STATISTIK HARI INI
    =====================================
    */
    public function getStats()
    {
        $today = date('Y-m-d');

        // total siswa akademik aktif
        $totalSiswa = $this->siswaAkademikModel
            ->where('status_akademik_id', 1)
            ->countAllResults();

        $hadir = $this->attendanceModel
            ->where('tanggal', $today)
            ->where('status', 'hadir')
            ->countAllResults();

        $izin = $this->attendanceModel
            ->where('tanggal', $today)
            ->where('status', 'izin')
            ->countAllResults();

        $sakit = $this->attendanceModel
            ->where('tanggal', $today)
            ->where('status', 'sakit')
            ->countAllResults();

        $alpha = max(0, $totalSiswa - ($hadir + $izin + $sakit));

        return $this->response->setJSON([
            'total' => $totalSiswa,
            'hadir' => $hadir,
            'izin'  => $izin,
            'sakit' => $sakit,
            'alpha' => $alpha
        ]);
    }

    /*
    =====================================
    STATISTIK PER JURUSAN
    =====================================
    */
    public function getJurusanStats()
    {
        $db = \Config\Database::connect();

        $query = $db->table('siswa_akademik sa')
            ->select('jurusan.nama_jurusan, COUNT(sa.id) as jumlah')
            ->join('jurusan', 'jurusan.id = sa.jurusan_id', 'left')
            ->where('sa.status_akademik_id', 1)
            ->groupBy('jurusan.id')
            ->orderBy('jurusan.nama_jurusan', 'ASC')
            ->get();

        return $this->response->setJSON(
            $query->getResultArray()
        );
    }

    /*
    =====================================
    STATISTIK PER KELAS
    =====================================
    */
    public function getKelasStats()
    {
        $db = \Config\Database::connect();

        $query = $db->table('siswa_akademik sa')
            ->select('kelas.nama_kelas, COUNT(sa.id) as jumlah')
            ->join('kelas', 'kelas.id = sa.kelas_id')
            ->where('sa.status_akademik_id', 1)
            ->groupBy('kelas.id')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->get();

        return $this->response->setJSON(
            $query->getResultArray()
        );
    }

    /*
    =====================================
    IZIN TERBARU
    =====================================
    */
    public function getLatestIzin()
    {
        $db = \Config\Database::connect();

        $query = $db->table('izin')
            ->select("
                izin.id,
                izin.jenis_izin,
                izin.isi_pesan,
                izin.status_izin,
                izin.created_at,
                siswa.nama_siswa
            ")
            ->join('siswa', 'siswa.id = izin.siswa_id', 'left')
            ->where('izin.status_izin', 'diajukan')
            ->orderBy('izin.created_at', 'DESC')
            ->limit(10)
            ->get();

        return $this->response->setJSON(
            $query->getResultArray()
        );
    }
}