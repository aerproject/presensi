<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Installer\InstallationIdentity;
use App\Libraries\Installer\EnvWriter;
use App\Libraries\License\ServerIdentity;
use App\Libraries\License\LicenseService;
use App\Libraries\License\LicenseCrypto;
use App\Libraries\License\LicenseCredentialStore;
use App\Libraries\License\FullUpgradeClient;
use App\Libraries\License\TrialBootstrapClient;
use App\Models\LicenseRuntimeModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

final class TrialInstall extends BaseController
{
    /**
     * Return canonical identity for this
     * PRESENSI application installation.
     *
     * Trial identity MUST use InstallationIdentity,
     * not physical server identity.
     */
    private function installationUuid(): string
    {
        return (
            new InstallationIdentity()
        )->uuid();
    }

    /**
     * Tahap 1 — Konfigurasi Awal.
     */
    public function index(): string
    {
        $checks = $this->initialChecks();

        return view('trial/initial', [
            'checks' => $checks,
            'allReady' => $checks['allReady'],
        ]);
    }

    /**
     * Tahap 2 — Pengecekan UUID.
     */
    public function uuidCheck(): string
    {
        $row = null;
        $credentialReady = false;

        /*
         * Canonical Trial identity.
         *
         * This UUID belongs to one PRESENSI
         * installation and is persisted outside
         * the application database.
         */
        $installationUuid =
            $this->installationUuid();

        /*
         * PRE-INSTALLATION UUID DECISION.
         *
         * Read-only check to License Server.
         * This MUST NOT claim or create a Trial license.
         */
        $uuidDecision = null;
        $uuidDecisionError = null;

        try {
            $decisionClient =
                new TrialBootstrapClient();

            $decision =
                $decisionClient->requestTrialDecision();

            $uuidDecision = strtoupper(
                trim(
                    (string) (
                        $decision['install_status']
                        ?? ''
                    )
                )
            );

            $uuidCanContinue =
                $decision['data']['can_continue']
                ?? null;

            $uuidStatus = strtolower(
                trim(
                    (string) (
                        $decision['data']['status']
                        ?? ''
                    )
                )
            );

            $uuidExpiresAt =
                $decision['data']['expires_at']
                ?? null;

            if (
                ! in_array(
                    $uuidDecision,
                    [
                        TrialBootstrapClient::RESPONSE_NEW_TRIAL,
                        TrialBootstrapClient::RESPONSE_EXISTING_TRIAL,
                        TrialBootstrapClient::RESPONSE_EXISTING_FULL,
                    ],
                    true
                )
            ) {
                throw new \RuntimeException(
                    'Status Installation UUID tidak dikenal.'
                );
            }

            session()->set([
                'trial_install_uuid_decision' =>
                    $uuidDecision,
                'trial_install_uuid_can_continue' =>
                    $uuidCanContinue,
                'trial_install_uuid_status' =>
                    $uuidStatus,
                'trial_install_uuid_expires_at' =>
                    $uuidExpiresAt,
                'trial_install_uuid' =>
                    $installationUuid,
            ]);
        } catch (\Throwable $e) {
            $uuidDecision = null;
            $uuidDecisionError =
                $e->getMessage();

            log_message(
                'error',
                'TRIAL_UUID_DECISION_ERROR='
                . $e->getMessage()
            );
        }

        return view('trial/install', [
            /*
             * New canonical Trial identity.
             */
            'installationUuid' =>
                $installationUuid,

            'installationUuidExists' =>
                $installationUuid !== '',

            /*
             * Read-only License Server decision.
             */
            'uuidDecision' =>
                $uuidDecision,

            'uuidDecisionError' =>
                $uuidDecisionError,

            'uuidCanContinue' =>
                $uuidCanContinue ?? null,

            'uuidStatus' =>
                $uuidStatus ?? '',

            'uuidExpiresAt' =>
                $uuidExpiresAt ?? null,

            'credentialReady' =>
                false,

            'runtime' =>
                $row,
        ]);
    }

    /**
     * Explicitly stop Trial installation.
     */
    public function stop(): RedirectResponse
    {
        return redirect()
            ->to('/')
            ->with(
                'success',
                'Instalasi Trial dihentikan.'
            );
    }

    /**
     * Existing Full installation terminal page.
     *
     * Installation UUID already belongs to a Full license.
     * Trial installation and upgrade must not continue.
     */
    public function existingFull(): string
    {
        $installationUuid = $this->installationUuid();

        return view(
            'trial/existing-full',
            [
                'installationUuid' => $installationUuid,
            ]
        );
    }

    /**
     * Full upgrade entry point.
     *
     * Existing Installation UUID is intentionally preserved.
     * No new identity is generated here.
     */
    public function upgrade(): string
    {
        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        $installationUuid =
            $this->installationUuid();

        $credentialStore = new LicenseCredentialStore(
            $runtime,
            new LicenseCrypto()
        );

        $credentialReady = $credentialStore->has();

        $licenseType = strtolower(
            trim((string) ($row['license_type'] ?? ''))
        );

        $activateStatus = strtolower(
            trim((string) ($row['activate_status'] ?? ''))
        );

        $validateStatus = strtolower(
            trim((string) ($row['validate_status'] ?? ''))
        );

        $fullLicenseActive =
            $licenseType === 'full'
            && $activateStatus === 'active'
            && $validateStatus === 'valid';

        return view('trial/upgrade', [
            'installationUuid' => $installationUuid,
            'installationUuidExists' => $installationUuid !== '',
            'credentialReady' => $credentialReady,
            'licenseType' => $licenseType,
            'activateStatus' => $activateStatus,
            'validateStatus' => $validateStatus,
            'fullLicenseActive' => $fullLicenseActive,
            'expiresAt' => $row['expires_at'] ?? null,
        ]);
    }

    /**
     * Display Full upgrade process state.
     */
    public function upgradeProcess(): string|ResponseInterface
    {
        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        $installationUuid =
            $this->installationUuid();

        $licenseType = strtolower(
            trim((string) ($row['license_type'] ?? ''))
        );

        $activateStatus = strtolower(
            trim((string) ($row['activate_status'] ?? ''))
        );

        $validateStatus = strtolower(
            trim((string) ($row['validate_status'] ?? ''))
        );

        if (
            $licenseType === 'full'
            && $activateStatus === 'active'
            && $validateStatus === 'valid'
        ) {
            return redirect()
                ->to('/trial/upgrade/status');
        }

        return view('trial/upgrade-process', [
            'installationUuid' => $installationUuid,
            'licenseType' => strtolower(
                trim((string) ($row['license_type'] ?? ''))
            ),
            'activateStatus' => strtolower(
                trim((string) ($row['activate_status'] ?? ''))
            ),
            'validateStatus' => strtolower(
                trim((string) ($row['validate_status'] ?? ''))
            ),
            'expiresAt' => $row['expires_at'] ?? null,
        ]);
    }

    /**
     * Display final Full upgrade status.
     */
    public function upgradeStatus(): string
    {
        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        $installationUuid =
            $this->installationUuid();

        return view('trial/upgrade-status', [
            'installationUuid' => $installationUuid,
            'licenseType' => strtolower(
                trim((string) ($row['license_type'] ?? ''))
            ),
            'activateStatus' => strtolower(
                trim((string) ($row['activate_status'] ?? ''))
            ),
            'validateStatus' => strtolower(
                trim((string) ($row['validate_status'] ?? ''))
            ),
            'expiresAt' => $row['expires_at'] ?? null,
        ]);
    }

    /**
     * Receive Full Upgrade form.
     *
     * Stage 4B only validates the UI input and current identity.
     * No bootstrap and no activation is executed yet.
     */
    public function submitUpgrade(): ResponseInterface
    {
        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        $installationUuid =
            $this->installationUuid();

        if ($installationUuid === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->with(
                    'error',
                    'Upgrade Full hanya dapat dilakukan pada instalasi yang sudah memiliki Installation UUID.'
                );
        }

        $licenseKey = strtoupper(
            trim(
                (string) $this->request->getPost('license_key')
            )
        );

        $apiKey = trim(
            (string) $this->request->getPost('api_key')
        );

        if ($licenseKey === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->withInput()
                ->with(
                    'error',
                    'Full License Key wajib diisi.'
                );
        }

        if ($apiKey === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->withInput()
                ->with(
                    'error',
                    'Full API Key wajib diisi.'
                );
        }

        return redirect()
            ->to('/trial/upgrade')
            ->withInput()
            ->with(
                'success',
                'Data upgrade diterima. Tahap bootstrap dan activation belum dijalankan.'
            );
    }

    /**
     * Bootstrap Full operational credential from UI.
     *
     * No activation is performed in this stage.
     */
    public function bootstrapUpgrade(): ResponseInterface
    {
        log_message(
            'error',
            'FULL_UPGRADE_BOOTSTRAP_ENTERED'
        );

        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        $installationUuid =
            $this->installationUuid();

        if ($installationUuid === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->with(
                    'error',
                    'Upgrade Full hanya dapat dilakukan pada instalasi yang sudah memiliki Installation UUID.'
                );
        }

        $licenseKey = strtoupper(
            trim(
                (string) $this->request->getPost('license_key')
            )
        );

        $apiKey = trim(
            (string) $this->request->getPost('api_key')
        );

        if ($licenseKey === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->withInput()
                ->with(
                    'error',
                    'Full License Key wajib diisi.'
                );
        }

        if ($apiKey === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->withInput()
                ->with(
                    'error',
                    'Full API Key wajib diisi.'
                );
        }

        try {
            $client = new FullUpgradeClient();

            $credential = $client->bootstrap(
                $licenseKey,
                $apiKey
            );

            if (
                ($credential['license_type'] ?? null) !== 'full'
            ) {
                throw new \RuntimeException(
                    'License Server tidak mengembalikan credential Full.'
                );
            }

            $store = new LicenseCredentialStore(
                $runtime,
                new LicenseCrypto()
            );

            $store->store(
                $credential['api_key'],
                $credential['api_secret']
            );

            return redirect()
                ->to('/trial/upgrade/process')
                ->with(
                    'success',
                    'Credential Full berhasil diperoleh dan disimpan secara aman. Lanjutkan proses aktivasi Full.'
                );

        } catch (\Throwable $e) {
            return redirect()
                ->to('/trial/upgrade')
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    /**
     * Perform Full activation from the UI.
     *
     * Runtime credentials are resolved from the encrypted
     * LicenseCredentialStore. No API Secret is accepted
     * from the browser.
     */
    public function activateUpgrade(): ResponseInterface
    {
        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        if (!is_array($row)) {
            return redirect()
                ->to('/trial/upgrade')
                ->with(
                    'error',
                    'Runtime lisensi tidak ditemukan.'
                );
        }

        $installationUuid =
            $this->installationUuid();

        if ($installationUuid === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->with(
                    'error',
                    'Installation UUID belum tersedia.'
                );
        }

        $licenseType = strtolower(
            trim((string) ($row['license_type'] ?? ''))
        );

        $activateStatus = strtolower(
            trim((string) ($row['activate_status'] ?? ''))
        );

        if (
            $licenseType === 'full'
            && $activateStatus === 'active'
        ) {
            return redirect()
                ->to('/trial/upgrade/status')
                ->with(
                    'success',
                    'Lisensi Full sudah aktif.'
                );
        }

        try {
            $service = new LicenseService(
                runtime: $runtime
            );

            $result = $service->activate();

            $data = $result['data'] ?? [];

            if (
                ($result['success'] ?? false) !== true
                || !is_array($data)
            ) {
                throw new \RuntimeException(
                    (string) (
                        $result['message']
                        ?? 'Aktivasi Full gagal.'
                    )
                );
            }

            $validateResult = $service->validate();

            if (
                ($validateResult['success'] ?? false) !== true
                || !is_array(
                    $validateResult['data'] ?? null
                )
                || strtolower(
                    trim(
                        (string) (
                            $validateResult['data']['status']
                            ?? ''
                        )
                    )
                ) !== 'valid'
            ) {
                throw new \RuntimeException(
                    (string) (
                        $validateResult['message']
                        ?? 'Validasi lisensi Full gagal.'
                    )
                );
            }

            return redirect()
                ->to('/trial/upgrade/status')
                ->with(
                    'success',
                    'Upgrade ke Full berhasil. Lisensi Full sudah aktif pada server ini.'
                );

        } catch (\Throwable $e) {
            return redirect()
                ->to('/trial/upgrade')
                ->with(
                    'error',
                    'Aktivasi Full gagal: ' .
                    $e->getMessage()
                );
        }
    }

    /**
     * Bootstrap Trial operational credential from UI.
     *
     * License key and API key come only from installer form.
     * API secret is returned by License Server and stored encrypted.
     */
    /**
     * Tahap 4 — Halaman Konfigurasi.
     *
     * Hanya menampilkan ringkasan konfigurasi sebelum proses
     * konfigurasi aplikasi dan aktivasi Trial dijalankan.
     */
    public function configuration(): string|ResponseInterface
    {
        $database = session()->get('trial_install_database');

        if (! is_array($database)) {
            return redirect()
                ->to('/trial/install/database')
                ->with(
                    'error',
                    'Konfigurasi database Tahap 3 belum tersedia.'
                );
        }

        return view('trial/configuration', [
            'installationUuid' => $this->installationUuid(),
            'database' => $database,
        ]);
    }

    /**
     * Tahap 4 — Proses Konfigurasi Aplikasi dan Lisensi.
     *
     * Menulis .env lalu menjalankan Claim, Store Credential,
     * Store License Key, Activate, dan Validate Trial.
     */
    public function processConfiguration(): string|ResponseInterface
    {
        $database = session()->get('trial_install_database');

        if (
            ! is_array($database)
            || ! ($database['hostname'] ?? null)
            || ! ($database['database'] ?? null)
            || ! ($database['username'] ?? null)
        ) {
            return redirect()
                ->to('/trial/install/database')
                ->with(
                    'error',
                    'Konfigurasi database Tahap 3 belum tersedia.'
                );
        }

        try {
            $appConfig = config('App');
            $licenseConfig = config('License');

            $encryptionKey =
                (string) env('encryption.key', '');

            if ($encryptionKey === '') {
                $encryptionKey = bin2hex(
                    random_bytes(32)
                );
            }

            $envWriter = new EnvWriter();

            $envWriter->write([
                'CI_ENVIRONMENT' =>
                    env(
                        'CI_ENVIRONMENT',
                        'production'
                    ),

                'app.baseURL' =>
                    (string) $appConfig->baseURL,

                'database.default.hostname' =>
                    $database['hostname'],

                'database.default.database' =>
                    $database['database'],

                'database.default.username' =>
                    $database['username'],

                'database.default.password' =>
                    $database['password'],

                'database.default.port' =>
                    $database['port'],

                'encryption.key' =>
                    $encryptionKey,

                'LICENSE_BASE_URL' =>
                    (string) $licenseConfig->baseUrl,

                'LICENSE_APP_CODE' =>
                    (string) $licenseConfig->appCode,

                'LICENSE_APP_VERSION' =>
                    (string) $licenseConfig->appVersion,

                'LICENSE_TIMEOUT' =>
                    (int) $licenseConfig->timeout,

                'LICENSE_API_KEY' => '',

                'LICENSE_API_SECRET' => '',

                'LICENSE_SECRET' =>
                    (string) env(
                        'LICENSE_SECRET',
                        ''
                    ),
            ]);

            session()->set([
                'trial_install_env_written' => true,
            ]);

            return $this->bootstrapTrial();

        } catch (\Throwable $e) {

            log_message(
                'error',
                'INSTALL_CONFIG_ERROR='
                . $e->getMessage()
            );

            return redirect()
                ->to('/trial/install/database')
                ->with(
                    'error',
                    'Konfigurasi aplikasi gagal: '
                    . $e->getMessage()
                );
        }
    }

    /**
     * Request and activate Trial License automatically.
     *
     * No Trial License Key or API Key is entered
     * manually by the installer.
     *
     * Flow:
     *
     * 1. Check local installation guard.
     * 2. Generate/read Installation UUID.
     * 3. Request Trial License automatically.
     * 4. License Server checks Installation UUID.
     * 5. Receive Trial credential.
     * 6. Store encrypted credential.
     * 7. Store Trial License Key.
     * 8. Activate Trial.
     * 9. Validate Trial.
     */
    public function bootstrapTrial(): string|ResponseInterface
    {
        $installationUuid = $this->installationUuid();

        /*
         * Resume a previously completed Trial installation.
         *
         * This is important when the database import succeeded and
         * Trial activation/validation already completed before the
         * final installation UI was introduced.
         */
        $existingRuntime = new LicenseRuntimeModel();
        $existingRow = $existingRuntime->getRuntime();

        if (
            is_array($existingRow)
            && strtolower(
                trim((string) ($existingRow['license_type'] ?? ''))
            ) === 'trial'
            && strtolower(
                trim((string) ($existingRow['activate_status'] ?? ''))
            ) === 'active'
            && strtolower(
                trim((string) ($existingRow['validate_status'] ?? ''))
            ) === 'valid'
        ) {
            $stepsStatus = [
                'store' => [
                    'status' => 'success',
                    'message' =>
                        'API credential Trial sudah tersimpan secara terenkripsi.',
                ],
                'license_key' => [
                    'status' => 'success',
                    'message' =>
                        'Trial License Key sudah tersimpan.',
                ],
                'activate' => [
                    'status' => 'success',
                    'message' =>
                        'Lisensi Trial sudah aktif.',
                ],
                'validate' => [
                    'status' => 'success',
                    'message' =>
                        'Lisensi Trial sudah tervalidasi.',
                ],
                'final' => [
                    'status' => 'success',
                    'message' =>
                        'Runtime lisensi ditemukan dalam kondisi valid.',
                ],
            ];

            if (
                !is_file(ROOTPATH . '.env')
                || !is_readable(ROOTPATH . '.env')
            ) {
                $stepsStatus['final'] = [
                    'status' => 'error',
                    'message' =>
                        'File .env tidak tersedia.',
                ];

                return view('trial/license-process', [
                    'installationUuid' => $installationUuid,
                    'licenseType' => 'trial',
                    'licenseStatus' => 'PROCESS ERROR',
                    'stepsStatus' => $stepsStatus,
                    'overallStatus' => 'error',
                ]);
            }

            return view('trial/license-process', [
                'installationUuid' => $installationUuid,
                'licenseType' => 'trial',
                'licenseStatus' => 'ACTIVE / VALID',
                'stepsStatus' => $stepsStatus,
                'overallStatus' => 'success',
            ]);
        }

        $stepsStatus = [
            'store' => [
                'status' => 'pending',
                'message' => 'Belum diproses.',
            ],
            'license_key' => [
                'status' => 'pending',
                'message' => 'Belum diproses.',
            ],
            'activate' => [
                'status' => 'pending',
                'message' => 'Belum diproses.',
            ],
            'validate' => [
                'status' => 'pending',
                'message' => 'Belum diproses.',
            ],
            'final' => [
                'status' => 'pending',
                'message' => 'Belum diproses.',
            ],
        ];

        $currentStep = 'store';

        try {
            log_message(
                'info',
                'TRIAL_INSTALL_REQUEST_ENTERED'
            );

            /*
             * Request Trial License automatically.
             */
            $client = new TrialBootstrapClient();
            $credential = $client->requestTrial();

            log_message(
                'info',
                'TRIAL_CREDENTIAL_RECEIVED=' . json_encode(
                    [
                        'response_state' =>
                            $credential['response_state'] ?? null,
                        'license_type' =>
                            $credential['license_type'] ?? null,
                        'status' =>
                            $credential['status'] ?? null,
                        'has_license_key' =>
                            !empty($credential['license_key']),
                        'has_api_key' =>
                            !empty($credential['api_key']),
                        'has_api_secret' =>
                            !empty($credential['api_secret']),
                    ],
                    JSON_UNESCAPED_SLASHES
                    | JSON_UNESCAPED_UNICODE
                )
            );

            $responseState = strtoupper(
                trim(
                    (string) (
                        $credential['response_state']
                        ?? ''
                    )
                )
            );

            if (
                $responseState ===
                TrialBootstrapClient::RESPONSE_EXISTING_TRIAL
            ) {
                return redirect()
                    ->to('/trial/upgrade')
                    ->with(
                        'error',
                        (string) (
                            $credential['message']
                            ?? 'Installation UUID ini sudah memiliki '
                            . 'lisensi Trial.'
                        )
                    );
            }

            if (
                $responseState ===
                TrialBootstrapClient::RESPONSE_EXISTING_FULL
            ) {
                return redirect()
                    ->to('/trial/install/full-exists')
                    ->with(
                        'error',
                        (string) (
                            $credential['message']
                            ?? 'Installation UUID ini sudah pernah '
                            . 'terdaftar menggunakan lisensi Full.'
                        )
                    );
            }

            if (
                $responseState !==
                TrialBootstrapClient::RESPONSE_NEW_TRIAL
            ) {
                throw new \RuntimeException(
                    'License Server mengembalikan '
                    . 'status instalasi tidak dikenal.'
                );
            }

            $licenseKey = strtoupper(
                trim(
                    (string) (
                        $credential['license_key']
                        ?? ''
                    )
                )
            );

            $apiKey = trim(
                (string) (
                    $credential['api_key']
                    ?? ''
                )
            );

            $apiSecret = trim(
                (string) (
                    $credential['api_secret']
                    ?? ''
                )
            );

            $licenseType = strtolower(
                trim(
                    (string) (
                        $credential['license_type']
                        ?? ''
                    )
                )
            );

            if ($licenseKey === '') {
                throw new \RuntimeException(
                    'License Server tidak mengembalikan Trial License Key.'
                );
            }

            if ($apiKey === '') {
                throw new \RuntimeException(
                    'License Server tidak mengembalikan API Key.'
                );
            }

            if ($apiSecret === '') {
                throw new \RuntimeException(
                    'License Server tidak mengembalikan API Secret.'
                );
            }

            if ($licenseType !== 'trial') {
                throw new \RuntimeException(
                    'License Server tidak mengembalikan lisensi Trial.'
                );
            }

            $runtime = new LicenseRuntimeModel();

            /*
             * 1. Store encrypted API credential.
             */
            $currentStep = 'store';

            $store = new LicenseCredentialStore(
                $runtime,
                new LicenseCrypto()
            );

            $store->store(
                $apiKey,
                $apiSecret
            );

            $stepsStatus['store'] = [
                'status' => 'success',
                'message' =>
                    'API credential berhasil disimpan secara terenkripsi.',
            ];

            /*
             * 2. Store Trial License Key.
             */
            $currentStep = 'license_key';

            $service = new LicenseService(
                runtime: $runtime
            );

            $service->storeLicense(
                $licenseKey
            );

            $stepsStatus['license_key'] = [
                'status' => 'success',
                'message' =>
                    'Trial License Key berhasil disimpan.',
            ];

            /*
             * 3. Activate Trial.
             */
            $currentStep = 'activate';

            $result = $service->activate(
                $licenseKey
            );

            if (
                !is_array($result)
                ||
                ($result['success'] ?? false) !== true
            ) {
                throw new \RuntimeException(
                    (string) (
                        $result['message']
                        ?? 'Aktivasi Trial gagal.'
                    )
                );
            }

            $stepsStatus['activate'] = [
                'status' => 'success',
                'message' =>
                    'Lisensi Trial berhasil diaktifkan.',
            ];

            /*
             * 4. Validate Trial.
             */
            $currentStep = 'validate';

            $validateResult = $service->validate();

            if (
                !is_array($validateResult)
                ||
                ($validateResult['success'] ?? false) !== true
            ) {
                throw new \RuntimeException(
                    (string) (
                        $validateResult['message']
                        ?? 'Validasi Trial gagal.'
                    )
                );
            }

            $stepsStatus['validate'] = [
                'status' => 'success',
                'message' =>
                    'Lisensi Trial berhasil divalidasi.',
            ];

            /*
             * 5. Final Installation Verification.
             */
            $currentStep = 'final';

            $requiredTables = [
                'users',
                'admin',
                'license_runtime',
            ];

            $db = \Config\Database::connect();
            $missingTables = [];

            foreach ($requiredTables as $table) {
                if (!$db->tableExists($table)) {
                    $missingTables[] = $table;
                }
            }

            if ($missingTables !== []) {
                throw new \RuntimeException(
                    'Final Installation gagal. Tabel wajib tidak ditemukan: '
                    . implode(', ', $missingTables)
                );
            }

            $runtimeRow = $runtime->getRuntime();

            if (!is_array($runtimeRow)) {
                throw new \RuntimeException(
                    'Final Installation gagal. Runtime lisensi tidak ditemukan.'
                );
            }

            if (
                strtolower(
                    trim(
                        (string) (
                            $runtimeRow['validate_status'] ?? ''
                        )
                    )
                ) !== 'valid'
            ) {
                throw new \RuntimeException(
                    'Final Installation gagal. Status validasi runtime belum valid.'
                );
            }

            if (
                !is_file(ROOTPATH . '.env')
                ||
                !is_readable(ROOTPATH . '.env')
            ) {
                throw new \RuntimeException(
                    'Final Installation gagal. File .env tidak tersedia.'
                );
            }

            $stepsStatus['final'] = [
                'status' => 'success',
                'message' =>
                    'Database, environment, dan runtime lisensi telah diverifikasi.',
            ];

            log_message(
                'info',
                'TRIAL_INSTALL_COMPLETED'
            );

            return view('trial/license-process', [
                'installationUuid' => $installationUuid,
                'licenseType' => $licenseType,
                'licenseStatus' => 'ACTIVE / VALID',
                'stepsStatus' => $stepsStatus,
                'overallStatus' => 'success',
            ]);

        } catch (\Throwable $e) {
            $stepsStatus[$currentStep] = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];

            log_message(
                'error',
                'TRIAL_INSTALL_ERROR='
                . $e->getMessage()
            );

            return view('trial/license-process', [
                'installationUuid' => $installationUuid,
                'licenseType' => 'trial',
                'licenseStatus' => 'PROCESS ERROR',
                'stepsStatus' => $stepsStatus,
                'overallStatus' => 'error',
            ]);
        }
    }


    /**
     * Display Create Administrator step after successful
     * Trial activation, validation, and final installation.
     */
    public function admin(): string|ResponseInterface
    {
        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        if (!is_array($row)) {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    'Runtime lisensi belum tersedia.'
                );
        }

        $licenseType = strtolower(
            trim((string) ($row['license_type'] ?? ''))
        );

        $activateStatus = strtolower(
            trim((string) ($row['activate_status'] ?? ''))
        );

        $validateStatus = strtolower(
            trim((string) ($row['validate_status'] ?? ''))
        );

        if (
            $licenseType !== 'trial'
            || $activateStatus !== 'active'
            || $validateStatus !== 'valid'
        ) {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    'Tahap lisensi Trial belum berhasil. '
                    . 'Create Admin belum dapat dilakukan.'
                );
        }

        foreach (['users', 'admin'] as $table) {
            if (! \Config\Database::connect()->tableExists($table)) {
                return redirect()
                    ->to('/trial/install')
                    ->with(
                        'error',
                        'Tabel wajib untuk Create Admin tidak tersedia: '
                        . $table
                    );
            }
        }

        return view('trial/final', [
            'installationUuid' =>
                $this->installationUuid(),
            'licenseStatus' =>
                'ACTIVE / VALID',
        ]);
    }

    /**
     * Create the first administrator account.
     *
     * Creates the users record and its admin profile atomically.
     */
    public function finalizeAdmin(): string|ResponseInterface
    {
        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        if (!is_array($row)) {
            return redirect()
                ->to('/trial/install/admin')
                ->with(
                    'error',
                    'Runtime lisensi belum tersedia.'
                );
        }

        $licenseType = strtolower(
            trim((string) ($row['license_type'] ?? ''))
        );

        $activateStatus = strtolower(
            trim((string) ($row['activate_status'] ?? ''))
        );

        $validateStatus = strtolower(
            trim((string) ($row['validate_status'] ?? ''))
        );

        if (
            $licenseType !== 'trial'
            || $activateStatus !== 'active'
            || $validateStatus !== 'valid'
        ) {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    'Lisensi Trial belum berstatus ACTIVE / VALID.'
                );
        }

        $namaAdmin = trim(
            (string) $this->request->getPost('nama_admin')
        );

        $waAdmin = trim(
            (string) $this->request->getPost('wa_admin')
        );

        $username = trim(
            (string) $this->request->getPost('username')
        );

        $email = trim(
            (string) $this->request->getPost('email')
        );

        $password = (string) $this->request->getPost('password');

        $passwordConfirm = (string) (
            $this->request->getPost('password_confirm')
        );

        $errors = [];

        if ($namaAdmin === '') {
            $errors[] = 'Nama Administrator wajib diisi.';
        } elseif (mb_strlen($namaAdmin) > 100) {
            $errors[] = 'Nama Administrator maksimal 100 karakter.';
        }

        if ($waAdmin === '') {
            $errors[] = 'No. WhatsApp Administrator wajib diisi.';
        } elseif (mb_strlen($waAdmin) > 20) {
            $errors[] = 'No. WhatsApp Administrator maksimal 20 karakter.';
        }

        if ($username === '') {
            $errors[] = 'Username Administrator wajib diisi.';
        } elseif (mb_strlen($username) > 50) {
            $errors[] = 'Username Administrator maksimal 50 karakter.';
        }

        if ($email === '') {
            $errors[] = 'Email Administrator wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email Administrator tidak valid.';
        } elseif (mb_strlen($email) > 100) {
            $errors[] = 'Email Administrator maksimal 100 karakter.';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Password Administrator minimal 6 karakter.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Konfirmasi password tidak sama.';
        }

        $db = \Config\Database::connect();

        if (
            $db->table('users')
                ->where('username', $username)
                ->countAllResults() > 0
        ) {
            $errors[] = 'Username Administrator sudah digunakan.';
        }

        if (
            $db->table('users')
                ->where('email', $email)
                ->countAllResults() > 0
        ) {
            $errors[] = 'Email Administrator sudah digunakan.';
        }

        if ($errors !== []) {
            return view('trial/final', [
                'installationUuid' =>
                    $this->installationUuid(),
                'licenseStatus' =>
                    'ACTIVE / VALID',
                'errors' => $errors,
                'namaAdmin' => $namaAdmin,
                'waAdmin' => $waAdmin,
                'username' => $username,
                'email' => $email,
            ]);
        }

        try {
            $db->transBegin();

            $userId = $db->table('users')->insert([
                'username' => $username,
                'email' => $email,
                'password' => password_hash(
                    $password,
                    PASSWORD_DEFAULT
                ),
                'role' => 'admin',
            ], true);

            if ($userId === false) {
                throw new \RuntimeException(
                    'Pembuatan user administrator gagal.'
                );
            }

            $adminId = $db->table('admin')->insert([
                'nama_admin' => $namaAdmin,
                'wa_admin' => $waAdmin,
                'user_id' => (int) $userId,
            ], true);

            if ($adminId === false) {
                throw new \RuntimeException(
                    'Pembuatan profil administrator gagal.'
                );
            }

            if (!$db->transStatus()) {
                $db->transRollback();

                throw new \RuntimeException(
                    'Transaksi pembuatan administrator gagal.'
                );
            }

            $db->transCommit();

            log_message(
                'info',
                'TRIAL_ADMIN_CREATED user_id='
                . (int) $userId
                . ' admin_id='
                . (int) $adminId
            );

            return view('trial/final', [
                'installationUuid' =>
                    $this->installationUuid(),
                'licenseStatus' =>
                    'ACTIVE / VALID',
                'adminCreated' => true,
                'adminUsername' => $username,
                'adminEmail' => $email,
            ]);

        } catch (\Throwable $e) {
            if ($db->transStatus() !== false) {
                $db->transRollback();
            }

            log_message(
                'error',
                'TRIAL_ADMIN_CREATE_ERROR='
                . $e->getMessage()
            );

            return view('trial/final', [
                'installationUuid' =>
                    $this->installationUuid(),
                'licenseStatus' =>
                    'ACTIVE / VALID',
                'error' =>
                    'Pembuatan administrator gagal: '
                    . $e->getMessage(),
                'namaAdmin' => $namaAdmin,
                'waAdmin' => $waAdmin,
                'username' => $username,
                'email' => $email,
            ]);
        }
    }

    /**
     * Revalidate existing Trial license after credential recovery.
     */
    public function validateTrial(): RedirectResponse
    {
        try {

            $runtime = new LicenseRuntimeModel();

            $service = new LicenseService(
                runtime: $runtime
            );

            $service->validate();

            return redirect()
                ->to('/trial/install')
                ->with(
                    'success',
                    'Validasi lisensi Trial berhasil.'
                );

        } catch (\Throwable $e) {

            log_message(
                'error',
                'TRIAL_VALIDATE_ERROR=' . $e->getMessage()
            );

            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }



    /**
     * Pemeriksaan Tahap 1 — Konfigurasi Awal.
     *
     * Tidak membuat database, tidak claim Trial,
     * dan tidak mengubah runtime License.
     *
     * @return array<string, mixed>
     */
    private function initialChecks(): array
    {
        $requiredExtensions = [
            'intl',
            'mbstring',
            'mysqli',
            'openssl',
            'pdo_mysql',
        ];

        $extensions = [];

        foreach ($requiredExtensions as $extension) {
            $extensions[$extension] = extension_loaded($extension);
        }

        $masterSql =
            ROOTPATH . 'database/masterpresensi_fresh.sql';

        $installationUuid = $this->installationUuid();

        $licenseConfig = config('License');

        $checks = [
            'php' => [
                'label' => 'PHP',
                'value' => PHP_VERSION,
                'ok' => version_compare(PHP_VERSION, '8.2.0', '>='),
            ],

            'codeigniter' => [
                'label' => 'CodeIgniter',
                'value' => \CodeIgniter\CodeIgniter::CI_VERSION,
                'ok' => true,
            ],

            'extensions' => [
                'label' => 'PHP Extensions',
                'value' => implode(
                    ', ',
                    array_keys(
                        array_filter($extensions)
                    )
                ),
                'ok' => ! in_array(false, $extensions, true),
                'details' => $extensions,
            ],

            'masterSql' => [
                'label' => 'Master SQL',
                'value' => $masterSql,
                'ok' => is_readable($masterSql),
            ],

            'installationUuid' => [
                'label' => 'Installation UUID',
                'value' => $installationUuid,
                'ok' => $installationUuid !== '',
            ],

            'licenseServer' => [
                'label' => 'Trial License Server',
                'value' => (string) $licenseConfig->baseUrl,
                'ok' => filter_var(
                    (string) $licenseConfig->baseUrl,
                    FILTER_VALIDATE_URL
                ) !== false,
            ],

            'appCode' => [
                'label' => 'Application Code',
                'value' => (string) $licenseConfig->appCode,
                'ok' => trim(
                    (string) $licenseConfig->appCode
                ) !== '',
            ],
        ];

        $checks['allReady'] = true;

        foreach ($checks as $key => $check) {
            if ($key === 'allReady') {
                continue;
            }

            if (($check['ok'] ?? false) !== true) {
                $checks['allReady'] = false;
            }
        }

        return $checks;
    }

    /**
     * Trial POST is intentionally blocked when an identity exists.
     */
    public function start(): ResponseInterface
    {
        $checks = $this->initialChecks();

        if (($checks['allReady'] ?? false) !== true) {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    'Konfigurasi awal belum memenuhi seluruh persyaratan instalasi.'
                );
        }

        session()->set([
            'trial_install_stage_1_complete' => true,
        ]);

        return redirect()
            ->to('/trial/install/uuid');
    }

    /**
     * Database Setup — display database configuration form.
     *
     * This stage does NOT connect, create, import, or modify
     * the application database. Those operations are handled
     * by the next installation stages.
     */
    public function database(): string|ResponseInterface
    {
        $rawDecision = session()->get(
            'trial_install_uuid_decision'
        );

        $decision = strtoupper(
            trim(
                (string) $rawDecision
            )
        );

        if ($this->request->is('post')) {
            log_message(
                'info',
                'INSTALL_DB_SESSION_DECISION='
                . ($decision !== '' ? $decision : 'EMPTY')
            );
        }

        /*
         * Tahap 3 hanya boleh dimasuki oleh Installation UUID
         * yang belum terdaftar pada License Server.
         *
         * EXISTING_TRIAL dan EXISTING_FULL bersifat terminal
         * pada Tahap 2 dan tidak boleh masuk ke Database Setup.
         */
        if (
            $decision !==
            TrialBootstrapClient::RESPONSE_NEW_TRIAL
        ) {
            return redirect()
                ->to('/trial/install/uuid')
                ->with(
                    'error',
                    'Database Setup hanya dapat dilakukan untuk Installation UUID baru.'
                );
        }

        /*
         * EXISTING_TRIAL wajib diverifikasi ulang langsung ke
         * License Server agar decision/session lama tidak dapat
         * dipakai untuk melewati status Trial expired.
         */
        if (
            $decision ===
            TrialBootstrapClient::RESPONSE_EXISTING_TRIAL
        ) {
            try {
                $freshDecision =
                    (new TrialBootstrapClient())
                        ->requestTrialDecision();

                $freshStatus = strtoupper(
                    trim(
                        (string) (
                            $freshDecision['install_status']
                            ?? ''
                        )
                    )
                );

                $freshCanContinue =
                    $freshDecision['data']['can_continue']
                    ?? null;

                if (
                    $freshStatus !==
                    TrialBootstrapClient::RESPONSE_EXISTING_TRIAL
                    || $freshCanContinue !== true
                ) {
                    session()->set([
                        'trial_install_uuid_decision' =>
                            $freshStatus,
                        'trial_install_uuid_can_continue' =>
                            $freshCanContinue,
                    ]);

                    return redirect()
                        ->to('/trial/install')
                        ->with(
                            'error',
                            'Lisensi Trial sudah expired. Upgrade ke Full diperlukan.'
                        );
                }

                session()->set([
                    'trial_install_uuid_can_continue' =>
                        true,
                    'trial_install_uuid_status' =>
                        strtolower(
                            trim(
                                (string) (
                                    $freshDecision['data']['status']
                                    ?? ''
                                )
                            )
                        ),
                    'trial_install_uuid_expires_at' =>
                        $freshDecision['data']['expires_at']
                        ?? null,
                ]);
            } catch (\Throwable $e) {
                log_message(
                    'error',
                    'INSTALL_DB_TRIAL_DECISION_RECHECK_ERROR='
                    . $e->getMessage()
                );

                return redirect()
                    ->to('/trial/install')
                    ->with(
                        'error',
                        'Status lisensi Trial tidak dapat diverifikasi. Instalasi dihentikan.'
                    );
            }
        }

        $db = [
            'hostname' => trim(
                (string) (
                    $this->request->getPost('hostname')
                    ?? 'localhost'
                )
            ),
            'port' => (int) (
                $this->request->getPost('port')
                ?? 3306
            ),
            'database' => trim(
                (string) (
                    $this->request->getPost('database')
                    ?? ''
                )
            ),
            'username' => trim(
                (string) (
                    $this->request->getPost('username')
                    ?? ''
                )
            ),
            'password' => (string) (
                $this->request->getPost('password')
                ?? ''
            ),
        ];

        if (
            $this->request->is('post')
        ) {
            log_message(
                'info',
                'INSTALL_DB_POST_ENTERED'
            );

            $errors = [];

            if ($db['hostname'] === '') {
                $errors[] = 'Database Host wajib diisi.';
            }

            if (
                $db['port'] < 1
                || $db['port'] > 65535
            ) {
                $errors[] = 'Database Port tidak valid.';
            }

            if ($db['database'] === '') {
                $errors[] = 'Database Name wajib diisi.';
            }

            if ($db['username'] === '') {
                $errors[] = 'Database Username wajib diisi.';
            }

            if ($errors !== []) {
                return view('trial/database', [
                    'installationUuid' =>
                        $this->installationUuid(),
                    'uuidDecision' =>
                        $decision,
                    'db' => $db,
                    'error' =>
                        implode(' ', $errors),
                ]);
            }

            try {
                $connection = new \mysqli(
                    $db['hostname'],
                    $db['username'],
                    $db['password'],
                    $db['database'],
                    $db['port']
                );

                if ($connection->connect_errno) {
                    throw new \RuntimeException(
                        'Koneksi database gagal: '
                        . $connection->connect_error
                    );
                }

                $connection->set_charset('utf8mb4');

                $sqlFile = ROOTPATH
                    . 'database/masterpresensi_fresh.sql';

                if (! is_readable($sqlFile)) {
                    throw new \RuntimeException(
                        'Master SQL tidak dapat dibaca: '
                        . $sqlFile
                    );
                }

                $sql = file_get_contents($sqlFile);

                if ($sql === false || trim($sql) === '') {
                    throw new \RuntimeException(
                        'Master SQL kosong atau gagal dibaca.'
                    );
                }

                if (! $connection->multi_query($sql)) {
                    throw new \RuntimeException(
                        'Import Master SQL gagal: '
                        . $connection->error
                    );
                }

                do {
                    if ($result = $connection->store_result()) {
                        $result->free();
                    }

                    if (
                        $connection->more_results()
                        && ! $connection->next_result()
                    ) {
                        throw new \RuntimeException(
                            'Import Master SQL gagal: '
                            . $connection->error
                        );
                    }
                } while ($connection->more_results());

                $connection->close();

                session()->set([
                    'trial_install_database' =>
                        $db,

                    'trial_install_database_tested' =>
                        true,

                    'trial_install_database_imported' =>
                        true,

                    'trial_install_env_written' =>
                        false,
                ]);

                return view('trial/database', [
                    'installationUuid' =>
                        $this->installationUuid(),

                    'uuidDecision' =>
                        $decision,

                    'db' =>
                        $db,

                    'connectionChecked' =>
                        true,

                    'databaseImported' =>
                        true,

                    'error' =>
                        null,

                    'success' =>
                        'Koneksi database berhasil dan Master SQL berhasil diimpor.',
                ]);
            } catch (\Throwable $e) {
                log_message(
                    'error',
                    'INSTALL_DB_ERROR=' . $e->getMessage()
                );

                return view('trial/database', [
                    'installationUuid' =>
                        $this->installationUuid(),
                    'uuidDecision' =>
                        $decision,
                    'db' => $db,
                    'error' =>
                        $e->getMessage(),
                ]);
            }
        }

        return view('trial/database', [
            'installationUuid' =>
                $this->installationUuid(),
            'uuidDecision' =>
                $decision,
            'db' => $db,
            'error' => null,
            'success' => null,
        ]);
    }

    /**
     * Recover Trial operational credential.
     *
     * This does NOT reinstall and does NOT activate again.
     * Existing server identity and activation are preserved.
     */
    public function recoverCredential(): ResponseInterface
    {
        log_message(
            'error',
            'RECOVER_CREDENTIAL_ENTERED'
        );

        $runtime = new LicenseRuntimeModel();

        $row = $runtime->getRuntime();

        if (!is_array($row)) {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    'License runtime belum tersedia.'
                );
        }

        $installationUuid =
            $this->installationUuid();

        if ($installationUuid === '') {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    'Installation UUID belum tersedia.'
                );
        }

        $credentialStore = new LicenseCredentialStore(
            $runtime,
            new LicenseCrypto()
        );

        if ($credentialStore->has()) {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'success',
                    'Credential sudah tersedia.'
                );
        }

        try {
            $licenseKey = (new LicenseService(
                runtime: $runtime
            ))->licenseKey();

            log_message(
                'error',
                'RECOVER_LICENSE_KEY_RESULT=' . ($licenseKey ?? 'NULL')
            );

            if ($licenseKey === null) {
                throw new \RuntimeException(
                    'License key tidak tersedia.'
                );
            }

            $client = new TrialBootstrapClient();

            log_message(
                'error',
                'RECOVER_CLIENT_CREATED'
            );

            $apiKey = trim(
                (string) $this->request->getPost('api_key')
            );

            log_message(
                'error',
                'RECOVER_API_KEY_LENGTH=' . strlen($apiKey)
            );

            if ($apiKey === '') {
                throw new \RuntimeException(
                    'Trial API Key wajib diisi.'
                );
            }

            $credential = $client->bootstrap(
                $licenseKey,
                $apiKey,
                true
            );

            log_message(
                'error',
                'RECOVER_BOOTSTRAP_SUCCESS'
            );

            $credentialStore->store(
                $credential['api_key'],
                $credential['api_secret']
            );

            $service = new LicenseService(
                runtime: $runtime
            );

            try {

                log_message(
                    'error',
                    'RECOVER_VALIDATE_START'
                );

                $validateResult = $service->validate();

                log_message(
                    'error',
                    'RECOVER_VALIDATE_SUCCESS=' .
                    json_encode(
                        [
                            'success' => $validateResult['success'] ?? false,
                            'message' => $validateResult['message'] ?? null,
                        ],
                        JSON_UNESCAPED_SLASHES
                    )
                );

            } catch (\Throwable $e) {

                log_message(
                    'error',
                    'RECOVER_VALIDATE_ERROR=' .
                    $e->getMessage()
                );

                throw $e;
            }

            return redirect()
                ->to('/trial/install')
                ->with(
                    'success',
                    'Credential Trial berhasil dipulihkan.'
                );

        } catch (\Throwable $e) {

            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

}
