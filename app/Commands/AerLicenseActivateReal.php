<?php

namespace App\Commands;

use App\Libraries\License\LicenseService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

class AerLicenseActivateReal extends BaseCommand
{
    protected $group = 'Temp';

    protected $name = 'aer:license-activate-real';

    protected $description = 'Activate the real PRESENSI AER license.';

    public function run(array $params)
    {
        CLI::write('===== AER LICENSE LIVE ACTIVATION =====');

        try {
            $service = new LicenseService();

            $result = $service->activate();

            CLI::write('ACTIVATE_RESULT=PASS');

            CLI::write(
                'SUCCESS=' .
                (($result['success'] ?? false) ? 'YES' : 'NO')
            );

            $data = $result['data'] ?? [];

            if (is_array($data)) {
                if (isset($data['server_id'])) {
                    CLI::write(
                        'SERVER_ID=' . $data['server_id']
                    );
                }

                if (isset($data['server_uuid'])) {
                    CLI::write(
                        'SERVER_UUID=' . $data['server_uuid']
                    );
                }

                if (isset($data['expires_at'])) {
                    CLI::write(
                        'EXPIRES_AT=' . $data['expires_at']
                    );
                }

                if (isset($data['status'])) {
                    CLI::write(
                        'STATUS=' . $data['status']
                    );
                }
            }

            return EXIT_SUCCESS;

        } catch (Throwable $e) {
            CLI::error('ACTIVATE_RESULT=FAIL');

            CLI::error(
                'EXCEPTION_CLASS=' . get_class($e)
            );

            CLI::error(
                'EXCEPTION_MESSAGE=' . $e->getMessage()
            );

            return EXIT_ERROR;
        }
    }
}
