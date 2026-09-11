<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseRuntimeModel extends Model
{
    protected $table      = 'license_runtime';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id',
        'license_id',
        'application_id',
        'license_type',
        'license_key_enc',
        'license_key_iv',
        'license_key_tag',
        'server_id',
        'server_uuid',
        'server_hash',
        'activate_status',
        'validate_status',
        'app_version',
        'activated_at',
        'last_validation_at',
        'last_heartbeat_at',
        'expires_at',
        'metadata_json',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;

    public function getRuntime(): ?array
    {
        return $this->where('id', 1)->first();
    }

    public function ensureRuntime(): array
    {
        $runtime = $this->getRuntime();

        if ($runtime) {
            return $runtime;
        }

        $now = date('Y-m-d H:i:s');

        $this->insert([
            'id'         => 1,
            'app_version'=> null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->getRuntime() ?? [];
    }

    public function updateRuntime(array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->where('id', 1)->set($data)->update();
    }
}
