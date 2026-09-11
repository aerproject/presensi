<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'siswa_id' => [
                'type'     => 'INT',
                'unsigned' => true
            ],
            'tanggal' => [
                'type' => 'DATE'
            ],
            'jam_masuk' => [
                'type' => 'TIME',
                'null' => true
            ],
            'jam_pulang' => [
                'type' => 'TIME',
                'null' => true
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => 'CURRENT_TIMESTAMP',
                'on update' => 'CURRENT_TIMESTAMP'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('attendance');
    }

    public function down()
    {
        $this->forge->dropTable('attendance');
    }
}
