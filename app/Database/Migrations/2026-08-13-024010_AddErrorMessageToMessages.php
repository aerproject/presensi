<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddErrorMessageToMessages extends Migration
{
    public function up()
    {
        $this->forge->addColumn('messages', [
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
                'after' => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('messages', 'error_message');
    }
}
