<?php

namespace App\Models;

use CodeIgniter\Model;

class AplikasiModel extends Model
{
    protected $table      = 'pengaturan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama_aplikasi',
        'nama_sekolah',
        'tahun_pelajaran',
        'hari_kerja',
        'jam_masuk',
        'jam_pulang',
        'kelas_pagi_masuk',
        'kelas_pagi_pulang',
        'kelas_siang_masuk',
        'kelas_siang_pulang',
        'kop_surat',
        'logo_sekolah',
        'created_at',
        'updated_at',
        'batas_masuk_sebelum',
        'batas_masuk_sesudah',
        'batas_pulang_sebelum',
        'batas_pulang_sesudah'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
