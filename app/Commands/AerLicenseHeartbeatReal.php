<?php

namespace App\Commands;

use App\Libraries\License\LicenseService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

class AerLicenseHeartbeatReal extends BaseCommand
{
    protected $group = 'Temp';

    protected $name = 'aer:license-heartbeat-real';

    protected $description = 'Send a real PRESENSI AER license heartbeat.';

    public function run(array $params)
    {
        CLI::write('===== AER LICENSE LIVE HEARTBEAT =====');

        try {
            $service = new LicenseService();

            $result = $service->heartbeat();

            CLI::write('HEARTBEAT_RESULT=PASS');

            CLI::write(
                'SUCCESS=' .
                (($result['success'] ?? false) ? 'YES' : 'NO')
            );

            $data = $result['data'] ?? [];

            if (is_array($data)) {
                if (isset($data['status'])) {
                    CLI::write(
                        'STATUS=' . $data['status']
                    );
                }

                if (isset($data['expires_at'])) {
                    CLI::write(
                        'EXPIRES_AT=' . $data['expires_at']
                    );
                }

                if (isset($data['server_id'])) {
                    CLI::write(
                        'SERVER_ID=' . $data['server_id']
                    );
                }
            }

            return EXIT_SUCCESS;

        } catch (Throwable $e) {
            CLI::error('HEARTBEAT_RESULT=FAIL');

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
