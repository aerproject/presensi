<?php

namespace App\Commands;

use App\Libraries\License\LicenseService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

class AerLicenseStoreReal extends BaseCommand
{
    protected $group = 'Temp';

    protected $name = 'aer:license-store-real';

    protected $description = 'Store the real PRESENSI AER license securely.';

    public function run(array $params)
    {
        CLI::write('===== AER LICENSE STORE REAL =====');

        try {
            $licenseKey = 'AER-FL-UHCH-GFQS-8282';

            $service = new LicenseService();

            $result = $service->storeLicense($licenseKey);

            CLI::write(
                'STORED=' . (($result['stored'] ?? false) ? 'YES' : 'NO')
            );

            CLI::write(
                'LICENSE=' . ($result['license_masked'] ?? 'UNKNOWN')
            );

            return EXIT_SUCCESS;

        } catch (Throwable $e) {
            CLI::error('STORE_FAILED');
            CLI::error('EXCEPTION_CLASS=' . get_class($e));
            CLI::error('EXCEPTION_MESSAGE=' . $e->getMessage());

            return EXIT_ERROR;
        }
    }
}
