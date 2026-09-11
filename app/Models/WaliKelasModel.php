<?php

namespace App\Models;

use CodeIgniter\Model;

class WaliKelasModel extends Model
{
	
    protected $table            = 'walikelas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama_walas', 'nohp_walas', 'created_at', 'updated_at'];
}
