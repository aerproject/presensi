<?php

namespace App\Models;

use CodeIgniter\Model;

class WapikeyModel extends Model
{
    protected $table      = 'wapikey';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'provider',
        'wa_api_key',
        'wa_api_url',
        'admin_phone',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil provider aktif dari tabel wapikey
     */
    public function getActiveProvider()
    {
        return $this->where('status', 'active')->first();
    }
}
