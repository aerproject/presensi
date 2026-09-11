<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Onesender extends BaseConfig
{
    public string $apiKey  = 'ISI_API_KEY_ONESENDER_ANDA';
    public string $baseUrl = 'https://app.onesender.net/api/send-message';
}
