<?php

namespace App\Models;

use CodeIgniter\Model;

class JamBelajarModel extends Model
{
    protected $table         = 'jam_belajar';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'shift',
        'jam_masuk',
        'jam_pulang'
    ];

    /*
    |--------------------------------------------------------------------------
    | AMBIL SHIFT BERDASARKAN ID
    |--------------------------------------------------------------------------
    */
    public function getById($id)
    {
        return $this->where('id', $id)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL SEMUA SHIFT
    |--------------------------------------------------------------------------
    */
    public function getAllShift()
    {
        return $this->orderBy('id', 'ASC')
                    ->findAll();
    }
}
