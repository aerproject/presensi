<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Throwable;

class AerLicenseRuntimeVerify extends BaseCommand
{
    protected $group = 'Temp';

    protected $name = 'aer:license-runtime-verify';

    protected $description = 'Verify stored AER license runtime state.';

    public function run(array $params)
    {
        CLI::write('===== AER LICENSE RUNTIME VERIFY =====');

        try {
            $db = Database::connect();

            $row = $db->table('license_runtime')
                ->where('id', 1)
                ->get()
                ->getRowArray();

            if (!$row) {
                CLI::error('RUNTIME_ROW=NOT_FOUND');
                return EXIT_ERROR;
            }

            CLI::write(
                'RUNTIME_ID=' . ($row['id'] ?? 'NULL')
            );

            CLI::write(
                'SERVER_UUID=' . ($row['server_uuid'] ?? 'NULL')
            );

            CLI::write(
                'LICENSE_KEY_ENC_PRESENT=' .
                (!empty($row['license_key_enc']) ? 'YES' : 'NO')
            );

            CLI::write(
                'LICENSE_KEY_IV_PRESENT=' .
                (!empty($row['license_key_iv']) ? 'YES' : 'NO')
            );

            CLI::write(
                'LICENSE_KEY_TAG_PRESENT=' .
                (!empty($row['license_key_tag']) ? 'YES' : 'NO')
            );

            $metadata = json_decode(
                (string) ($row['metadata_json'] ?? ''),
                true
            );

            if (!is_array($metadata)) {
                $metadata = [];
            }

            $credential =
                $metadata['license_credential'] ?? [];

            CLI::write(
                'CREDENTIAL_PRESENT=' .
                (is_array($credential) && !empty($credential)
                    ? 'YES'
                    : 'NO')
            );

            CLI::write(
                'API_KEY_ENC_PRESENT=' .
                (!empty($credential['api_key_enc'])
                    ? 'YES'
                    : 'NO')
            );

            CLI::write(
                'API_SECRET_ENC_PRESENT=' .
                (!empty($credential['api_secret_enc'])
                    ? 'YES'
                    : 'NO')
            );

            CLI::write(
                'ACTIVATE_STATUS=' .
                (($row['activate_status'] ?? '') === ''
                    ? 'NULL'
                    : $row['activate_status'])
            );

            CLI::write(
                'SERVER_ID=' .
                (($row['server_id'] ?? '') === ''
                    ? 'NULL'
                    : $row['server_id'])
            );

            CLI::write(
                'SERVER_HASH_PRESENT=' .
                (!empty($row['server_hash']) ? 'YES' : 'NO')
            );

            CLI::write(
                'ACTIVATED_AT=' .
                (($row['activated_at'] ?? '') === ''
                    ? 'NULL'
                    : $row['activated_at'])
            );

            CLI::write(
                'EXPIRES_AT=' .
                (($row['expires_at'] ?? '') === ''
                    ? 'NULL'
                    : $row['expires_at'])
            );

            $machineId = trim(
                (string) @file_get_contents('/etc/machine-id')
            );

            CLI::write('MACHINE_ID=' . $machineId);

            CLI::write(
                'IDENTITY_MATCH=' .
                (
                    ($row['server_uuid'] ?? '') === $machineId
                        ? 'PASS'
                        : 'FAIL'
                )
            );

            CLI::write('===== RUNTIME VERIFY COMPLETE =====');

            return EXIT_SUCCESS;

        } catch (Throwable $e) {
            CLI::error(
                'VERIFY_FAILED=' . get_class($e)
            );

            CLI::error(
                'MESSAGE=' . $e->getMessage()
            );

            return EXIT_ERROR;
        }
    }
}
