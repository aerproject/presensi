<?php

namespace App\Models;

class KelasModel extends BaseTimestampModel
{
    protected $table      = 'kelas';
    protected $primaryKey = 'id';

    // Field yang boleh diisi
    protected $allowedFields = [
        'kelas',
        'nama_kelas',
        'jurusan_id',
        'created_at',
        'updated_at'
    ];


    /**
     * Ambil semua kelas dengan jurusan (join)
     */
    public function getAllWithJurusan()
    {
        return $this->select('kelas.*, jurusan.nama_jurusan')
                    ->join('jurusan', 'jurusan.id = kelas.jurusan_id', 'left')
                    ->orderBy('kelas.id', 'ASC')
                    ->findAll();
    }
}
