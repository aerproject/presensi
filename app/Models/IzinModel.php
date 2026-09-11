<?php

namespace App\Models;

use CodeIgniter\Model;

class IzinModel extends Model
{
    protected $table      = 'izin';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'siswa_id',
        'siswa_akademik_id',
        'ortu_id',
        'jenis_izin',
        'isi_pesan',
        'upload_surat',
        'status_izin'
        // hapus created_at dan updated_at dari allowedFields karena diatur otomatis oleh CI4
    ];

    /*
    |--------------------------------------------------------------------------
    | Timestamp otomatis CI4
    |--------------------------------------------------------------------------
    */
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime'; // <--- WAJIB TAMBAHKAN INI agar formatnya cocok dengan DATETIME MySQL

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /*
    |--------------------------------------------------------------------------
    | Ambil semua data izin + relasi siswa akademik aktif saat izin dibuat
    |--------------------------------------------------------------------------
    */
    public function getIzinWithSiswa()
    {
        return $this->select("
                izin.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                jurusan.nama_jurusan
            ")
            ->join('siswa', 'siswa.id = izin.siswa_id', 'left')
            ->join('siswa_akademik', 'siswa_akademik.id = izin.siswa_akademik_id', 'left')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id', 'left')
            ->join('jurusan', 'jurusan.id = siswa_akademik.jurusan_id', 'left')
            ->orderBy('izin.created_at', 'DESC');
    }

    /*
    |--------------------------------------------------------------------------
    | Detail izin berdasarkan ID
    |--------------------------------------------------------------------------
    */
    public function getDetail($id)
    {
        return $this->select("
                izin.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                jurusan.nama_jurusan
            ")
            ->join('siswa', 'siswa.id = izin.siswa_id', 'left')
            ->join('siswa_akademik', 'siswa_akademik.id = izin.siswa_akademik_id', 'left')
            ->join('kelas', 'kelas.id = siswa_akademik.kelas_id', 'left')
            ->join('jurusan', 'jurusan.id = siswa_akademik.jurusan_id', 'left')
            ->where('izin.id', $id)
            ->first();
    }
}