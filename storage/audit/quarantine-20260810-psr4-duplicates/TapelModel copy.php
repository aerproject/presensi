<?php

namespace App\Models;

use CodeIgniter\Model;

class TapelModel extends Model
{
    protected $table            = 'tapel';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'tahun_pelajaran',
        'semester',
        'aktif',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $beforeInsert = ['addCreated'];
    protected $beforeUpdate = ['addUpdated'];

    protected function addCreated(array $data)
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function addUpdated(array $data)
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

}
