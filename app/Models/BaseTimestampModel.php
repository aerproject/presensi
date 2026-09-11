<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseTimestampModel extends Model
{
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['setCreatedAt'];
    protected $beforeUpdate = ['setUpdatedAt'];

    protected function setCreatedAt(array $data)
    {
        // Jika created_at belum ada, isi otomatis
        if (!isset($data['data'][$this->createdField])) {
            $data['data'][$this->createdField] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    protected function setUpdatedAt(array $data)
    {
        // Jangan menimpa created_at
        $data['data'][$this->updatedField] = date('Y-m-d H:i:s');
        return $data;
    }
}
