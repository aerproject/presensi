<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateLicenseRenewalRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'license_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'application_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'server_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'server_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],

            'server_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],

            'current_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'requested_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'pending',
            ],

            'submitted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'response_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('license_id');
        $this->forge->addKey('application_id');
        $this->forge->addKey('server_id');
        $this->forge->addKey('server_uuid');
        $this->forge->addKey('server_hash');
        $this->forge->addKey('status');
        $this->forge->addKey('submitted_by');
        $this->forge->addKey('submitted_at');

        $this->forge->createTable(
            'license_renewal_requests',
            true
        );
    }

    public function down()
    {
        $this->forge->dropTable(
            'license_renewal_requests',
            true
        );
    }
}
