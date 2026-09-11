<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddUniqueRequestUuidIndexToLicenseRenewalRequestsTable extends Migration
{
    public function up()
    {
        $this->db->query(
            'ALTER TABLE `license_renewal_requests`
             ADD UNIQUE KEY `uq_license_renewal_requests_request_uuid`
             (`request_uuid`)'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE `license_renewal_requests`
             DROP INDEX `uq_license_renewal_requests_request_uuid`'
        );
    }
}
