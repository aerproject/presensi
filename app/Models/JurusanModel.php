<?php

namespace App\Models;

class JurusanModel extends BaseTimestampModel
{
    protected $table            = 'jurusan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
    'kode_jurusan',
    'nama_jurusan',
];

}
