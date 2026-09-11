<?php

namespace App\Models;

use App\Models\BaseTimestampModel;

class GuruModel extends BaseTimestampModel
{
    protected $table            = 'guru';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';

    protected $allowedFields    = [
        'users_id',
        'nama_guru',
        'nip',
    ];
}
