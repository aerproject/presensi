<?php

namespace App\Models;

class KelasMappingModel extends BaseTimestampModel
{
    protected $table      = 'kelas_mapping';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'kelas_id',
        'next_kelas_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Ambil mapping berdasarkan kelas lama
     */
    public function getNextClass($kelasId)
    {
        return $this->where('kelas_id', $kelasId)->first();
    }

    /**
     * Ambil semua mapping dengan join nama kelas
     */
    public function getAllMapping()
    {
        return $this->select('kelas_mapping.*, k1.nama_kelas as kelas_lama, k2.nama_kelas as kelas_baru')
                    ->join('kelas as k1', 'k1.id = kelas_mapping.kelas_id')
                    ->join('kelas as k2', 'k2.id = kelas_mapping.next_kelas_id', 'left')
                    ->orderBy('kelas_mapping.kelas_id', 'ASC')
                    ->findAll();
    }
}
