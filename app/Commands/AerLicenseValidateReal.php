<?php

namespace App\Commands;

use App\Libraries\License\LicenseService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

class AerLicenseValidateReal extends BaseCommand
{
    protected $group = 'Temp';

    protected $name = 'aer:license-validate-real';

    protected $description = 'Validate the real PRESENSI AER license.';

    public function run(array $params)
    {
        CLI::write('===== AER LICENSE LIVE VALIDATION =====');

        try {
            $service = new LicenseService();

            $result = $service->validate();

            CLI::write('VALIDATE_RESULT=PASS');

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
            }

            return EXIT_SUCCESS;

        } catch (Throwable $e) {
            CLI::error('VALIDATE_RESULT=FAIL');

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
