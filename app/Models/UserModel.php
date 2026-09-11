<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'username',
        'email',
        'password',
        'role',
        'created_at',
        'updated_at'
    ];

    // Timestamp dikelola oleh database (CURRENT_TIMESTAMP)
    protected $useTimestamps = false;

    protected $returnType    = 'array';

    protected $validationRules = [
        'username' => 'required|is_unique[users.username]|max_length[50]',
        'email'    => 'required|valid_email|is_unique[users.email]|max_length[100]',
        'password' => 'required|min_length[6]',
        'role'     => 'required|in_list[admin,operator,siswa,ortu,walikelas]'
    ];
}
