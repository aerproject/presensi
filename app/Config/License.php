<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

class License extends BaseConfig
{
    public string $baseUrl;
    public string $apiKey;
    public string $apiSecret = '';
    public string $appVersion;
    public string $localSecret = '';
    public int $timeout = 15;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) env(
                'LICENSE_BASE_URL',
                'https://lisensi.aerproject.id'
            ),
            '/'
        );

        $this->apiKey = trim(
            (string) env('LICENSE_API_KEY', '')
        );

        $this->apiSecret = trim(
            (string) env('LICENSE_API_SECRET', '')
        );

        $this->appVersion = trim(
            (string) env('LICENSE_APP_VERSION', '1.0.0')
        );

        $this->localSecret = trim(
            (string) env('LICENSE_SECRET', '')
        );

        $this->timeout = (int) env(
            'LICENSE_TIMEOUT',
            15
        );
    }
}
