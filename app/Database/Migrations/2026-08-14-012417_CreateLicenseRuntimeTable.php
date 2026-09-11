<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLicenseRuntimeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'license_key_enc' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'license_key_iv' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'license_key_tag' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
            'activate_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'validate_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'app_version' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'activated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_validation_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_heartbeat_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'metadata_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('server_uuid');
        $this->forge->addKey('server_hash');
        $this->forge->addKey('validate_status');
        $this->forge->addKey('expires_at');

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('license_runtime', true);
    }

    public function down()
    {
        $this->forge->dropTable('license_runtime', true);
    }
}
