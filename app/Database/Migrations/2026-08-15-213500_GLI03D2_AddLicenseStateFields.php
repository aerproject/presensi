<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class GLI03D2_AddLicenseStateFields extends Migration
{
    public function up()
    {
        $fields = [
            'license_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],

            'application_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'license_id',
            ],

            'license_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'application_id',
            ],
        ];

        $this->forge->addColumn(
            'license_runtime',
            $fields
        );

        $this->forge->addKey(
            'license_id'
        );

        $this->forge->addKey(
            'application_id'
        );

        $this->forge->addKey(
            'license_type'
        );
    }

    public function down()
    {
        $this->forge->dropColumn(
            'license_runtime',
            [
                'license_id',
                'application_id',
                'license_type',
            ]
        );
    }
}
