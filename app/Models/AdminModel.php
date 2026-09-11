<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table      = 'admin';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama_admin',
        'wa_admin',
        'user_id',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
}
