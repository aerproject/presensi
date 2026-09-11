<?php

namespace App\Models;

use CodeIgniter\Model;

class BroadcastModel extends Model
{
    protected $table      = 'broadcast';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'judul',
        'jenis_pesan',
        'isi_pesan',
        'phone',        // untuk perorangan
        'student_id',
        'kelas_id',     // untuk pesan kelas
        'tingkat',      // untuk pesan tingkat
        'jurusan_id',   // untuk pesan jurusan
        'status',       // draft, queued, sent, failed, partial
        'waktu_kirim',
        'error_message'
        // 'created_at' dan 'updated_at' dilepas dari allowedFields karena diatur oleh useTimestamps
    ];

    /*
    |--------------------------------------------------------------------------
    | Timestamp Otomatis CodeIgniter 4
    |--------------------------------------------------------------------------
    */
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // <-- Memastikan format string DATETIME (Y-m-d H:i:s) cocok dengan MySQL

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /*
    |--------------------------------------------------------------------------
    | Model Events Hooks
    |--------------------------------------------------------------------------
    */
    protected $beforeUpdate = ['setWaktuKirim'];

    /**
     * Otomatis isi waktu_kirim jika status berubah menjadi sent, failed, atau partial
     */
    protected function setWaktuKirim(array $data)
    {
        if (isset($data['data']['status']) && in_array($data['data']['status'], ['sent', 'failed', 'partial'])) {
            $data['data']['waktu_kirim'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }
}