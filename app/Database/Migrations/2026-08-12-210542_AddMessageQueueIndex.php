<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMessageQueueIndex extends Migration
{
    public function up()
    {
        $this->db->query(
            'ALTER TABLE messages
             ADD INDEX idx_messages_queue (status, waktu_kirim)'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE messages
             DROP INDEX idx_messages_queue'
        );
    }
}
