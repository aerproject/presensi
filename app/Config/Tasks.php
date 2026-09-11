<?php

namespace Config;

use CodeIgniter\Tasks\Config\Tasks as BaseTasks;

class Tasks extends BaseTasks
{
    public function init()
    {
        // Jalankan worker broadcast setiap menit
        $this->call('App\Controllers\Admin\Broadcast::sendBroadcast')->everyMinute();
    }
}
