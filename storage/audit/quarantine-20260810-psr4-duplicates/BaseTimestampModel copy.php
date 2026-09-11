<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseTimestampModel extends Model
{
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

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
