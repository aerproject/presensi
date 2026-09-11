<?php

namespace App\Models;

class SiswaModel extends BaseTimestampModel
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'nis',
        'nama_siswa',
        'tahun_masuk',
        'wa_siswa',
        'users_id',
        'kelas_id',
        'jurusan_id',
        'ortu_id',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $returnType    = 'array';

    protected $validationRules = [
        'nis'         => 'required|string|max_length[20]',
        'nama_siswa'  => 'required|string|max_length[100]',
        'tahun_masuk' => 'permit_empty|integer|greater_than_equal_to[2000]',
        'wa_siswa'    => 'permit_empty|string|max_length[20]',
        'users_id'    => 'permit_empty|integer',
        'kelas_id'    => 'permit_empty|integer',
        'jurusan_id'  => 'permit_empty|integer',
        'ortu_id'     => 'permit_empty|integer',
        'status'      => 'permit_empty|string|max_length[20]'
    ];

    /**
     * Mengambil daftar siswa yang belum absen hari ini
     *
     * @param array $absenIds ID siswa yang sudah absen
     * @return array
     */
    public function getBelumAbsenHariIni(array $absenIds)
    {
        $builder = $this->select('siswa.*, kelas.nama_kelas')
                        ->join('kelas', 'kelas.id = siswa.kelas_id');

        // Filter hanya siswa aktif
        $builder->where('siswa.status', 'aktif');

        // Filter siswa yang belum absen hari ini
        if (!empty($absenIds)) {
            $builder->whereNotIn('siswa.id', $absenIds);
        }

        return $builder->findAll();
    }
}
