<?php

namespace App\Models;

use CodeIgniter\Model;

class TapelModel extends Model
{
    protected $table      = 'tapel';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'tahun_pelajaran',
        'semester',
        'aktif',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil tahun pelajaran aktif
     */
    public function getAktif()
    {
        return $this->where('aktif', 1)->first();
    }
}
