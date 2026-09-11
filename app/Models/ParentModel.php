<?php

namespace App\Models;

use CodeIgniter\Model;

class ParentModel extends Model
{
    protected $table            = 'parents';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama_ortu', 'wa_ortu', 'users_id'];
    protected $useTimestamps    = true;

    /**
     * Relasi ke tabel users
     */
    public function withUser()
    {
        return $this
            ->select('parents.*, users.username, users.email, users.role')
            ->join('users', 'users.id = parents.users_id', 'left');
    }
}
