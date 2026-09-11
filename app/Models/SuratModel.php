<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratModel extends Model
{
    protected $table = 'surat';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'siswa_id',
        'ortu_id',
        'jenis',
        'tanggal_absensi',
        'isi_pesan',
        'status',
        'upload_surat',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
}
