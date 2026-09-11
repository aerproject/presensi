<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'siswa_akademik_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    /*
    |--------------------------------------------------------------------------
    | ABSENSI HARI INI
    |--------------------------------------------------------------------------
    */
    public function getAbsensiHariIni()
    {
        $today = date('Y-m-d');

        return $this->select("
                attendance.id,
                attendance.tanggal,
                attendance.jam_masuk,
                attendance.jam_pulang,
                attendance.status,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                tapel.tahun_pelajaran,
                tapel.semester
            ")
            ->join(
                'siswa_akademik',
                'siswa_akademik.id = attendance.siswa_akademik_id',
                'left'
            )
            ->join(
                'siswa',
                'siswa.id = siswa_akademik.siswa_id',
                'left'
            )
            ->join(
                'kelas',
                'kelas.id = siswa_akademik.kelas_id',
                'left'
            )
            ->join(
                'tapel',
                'tapel.id = siswa_akademik.tapel_id',
                'left'
            )
            ->where('attendance.tanggal', $today)
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->findAll();
    }

    /*
    |--------------------------------------------------------------------------
    | STATISTIK MINGGUAN
    |--------------------------------------------------------------------------
    */
    public function getStatistikMingguan()
    {
        $start = date('Y-m-d', strtotime('monday this week'));
        $end   = date('Y-m-d', strtotime('sunday this week'));

        return $this->select("
                DAYNAME(attendance.tanggal) as hari,
                attendance.status,
                COUNT(*) as jumlah
            ")
            ->where('attendance.tanggal >=', $start)
            ->where('attendance.tanggal <=', $end)
            ->groupBy('hari, attendance.status')
            ->orderBy('attendance.tanggal', 'ASC')
            ->findAll();
    }

    /*
    |--------------------------------------------------------------------------
    | CEK ABSENSI HARI INI
    |--------------------------------------------------------------------------
    */
    public function cekAbsensiHariIni($siswaAkademikId, $tanggal)
    {
        return $this->where('siswa_akademik_id', $siswaAkademikId)
            ->where('tanggal', $tanggal)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | RIWAYAT ABSENSI SISWA
    |--------------------------------------------------------------------------
    */
    public function getRiwayatSiswa($siswaAkademikId)
    {
        return $this->where('siswa_akademik_id', $siswaAkademikId)
            ->orderBy('tanggal', 'DESC')
            ->findAll();
    }
}