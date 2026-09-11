<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddRequestUuidToLicenseRenewalRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn(
            'license_renewal_requests',
            [
                'request_uuid' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 36,
                    'null'       => false,
                    'after'      => 'id',
                ],
            ]
        );

        $this->forge->addKey(
            'request_uuid',
            true
        );
    }

    public function down()
    {
        $this->forge->dropColumn(
            'license_renewal_requests',
            'request_uuid'
        );
    }
}
