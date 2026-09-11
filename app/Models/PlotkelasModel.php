<?php

namespace App\Models;

class PlotkelasModel extends BaseTimestampModel
{
    protected $table      = 'plot_kelas';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'siswa_id',
        'kelas_id',
        'tapel_id',
        'shift',      
        'jam_masuk',
        'jam_pulang',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $returnType = 'array';

    // Relasi ke tabel siswa
    public function getSiswa($siswaId)
    {
        return $this->db->table('siswa')
                        ->where('id', $siswaId)
                        ->get()
                        ->getRowArray();
    }

    // Relasi ke tabel kelas
    public function getKelas($kelasId)
    {
        return $this->db->table('kelas')
                        ->where('id', $kelasId)
                        ->get()
                        ->getRowArray();
    }

    // Relasi ke tabel tapel
    public function getTapel($tapelId)
    {
        return $this->db->table('tapel')
                        ->where('id', $tapelId)
                        ->get()
                        ->getRowArray();
    }

    // Ambil semua plot kelas dengan join siswa, kelas, tapel + pagination
    public function getAllPlotKelas($perPage = 10)
    {
        return $this->select('plot_kelas.*, siswa.nama_siswa, kelas.nama_kelas, jurusan.nama_jurusan, tapel.tahun_pelajaran, tapel.semester')
                    ->join('siswa', 'siswa.id = plot_kelas.siswa_id')
                    ->join('kelas', 'kelas.id = plot_kelas.kelas_id')
                    ->join('jurusan', 'jurusan.id = kelas.jurusan_id', 'left')
                    ->join('tapel', 'tapel.id = plot_kelas.tapel_id')
                    ->orderBy('plot_kelas.id', 'ASC')   // ASCENDING
                    ->paginate($perPage);               // pagination
    }
}
