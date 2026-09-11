<?php

namespace App\Models;

use CodeIgniter\Model;

class QrCodeModel extends Model
{
    protected $table      = 'qrcodes';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nis',
        'kelas_id',
        'file_path',
        'created_at'
    ];

    protected $useTimestamps = false; 
}
