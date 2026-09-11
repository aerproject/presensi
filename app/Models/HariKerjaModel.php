<?php

namespace App\Models;

use CodeIgniter\Model;

class HariKerjaModel extends Model
{
    protected $table         = 'hari_kerja';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'hari',
        'aktif'
    ];

    /*
    |--------------------------------------------------------------------------
    | CEK APAKAH HARI INI HARI SEKOLAH
    |--------------------------------------------------------------------------
    */
    public function isHariKerja($hari)
    {
        return $this->where('hari', $hari)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL SEMUA HARI KERJA
    |--------------------------------------------------------------------------
    */
    public function getAllHari()
    {
        return $this->orderBy('id', 'ASC')
                    ->findAll();
    }
}