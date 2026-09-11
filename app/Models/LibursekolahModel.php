<?php

namespace App\Models;

use CodeIgniter\Model;

class LibursekolahModel extends Model
{
    protected $table            = 'libursekolah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Bisa diubah ke 'object' jika suka
    protected $protectFields    = true;
    
    // Daftarkan kolom yang boleh diisi (Mass Assignment)
    protected $allowedFields    = ['tapel_id', 'tanggal', 'keterangan'];

    // Fitur Otomatisasi Timestamp
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Opsional: Validasi sederhana
    protected $validationRules      = [
        'tapel_id'   => 'required|numeric',
        'tanggal'    => 'required|valid_date',
        'keterangan' => 'required|min_length[3]|max_length[255]',
    ];

    /**
     * Mengambil data libur beserta detail tahun pelajaran
     */
    public function getLiburLengkap()
    {
        return $this->select('libursekolah.*, tapel.tahun_pelajaran, tapel.semester')
                    ->join('tapel', 'tapel.id = libursekolah.tapel_id')
                    ->orderBy('libursekolah.tanggal', 'DESC');
    }
}