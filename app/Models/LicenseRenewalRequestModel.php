<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class LicenseRenewalRequestModel extends Model
{
    protected $table = 'license_renewal_requests';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'request_uuid',
        'license_id',
        'application_id',
        'server_id',
        'server_uuid',
        'server_hash',
        'current_expires_at',
        'requested_expires_at',
        'notes',
        'status',
        'submitted_by',
        'submitted_at',
        'processed_at',
        'response_message',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;
}
