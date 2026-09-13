<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Installer\InstallationIdentity;
use App\Libraries\License\TrialReinstallGuard;
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
    private function guard(): TrialReinstallGuard
    {
        return new TrialReinstallGuard(
            new LicenseRuntimeModel()
        );
    }

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

    public function index(): string
    {
        $guard = $this->guard();

        $runtime = new LicenseRuntimeModel();

        $row = $runtime->getRuntime();

        $credentialStore =
            new LicenseCredentialStore(
                $runtime,
                new LicenseCrypto()
            );

        /*
         * Canonical Trial identity.
         *
         * This UUID belongs to one PRESENSI
         * installation and is persisted outside
         * the application database.
         */
        $installationUuid =
            $this->installationUuid();

        return view('trial/install', [
            'allowed' =>
                $guard->isAllowed(),

            'reason' =>
                $guard->reason(),

            'notice' =>
                $guard->notice(),

            /*
             * New canonical Trial identity.
             */
            'installationUuid' =>
                $installationUuid,

            'installationUuidExists' =>
                $installationUuid !== '',

            'credentialReady' =>
                $credentialStore->has(),

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
    public function bootstrapTrial(): ResponseInterface
    {
        $guard = $this->guard();

        if (!$guard->isAllowed()) {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    $guard->notice()
                    ?? 'Instalasi Trial tidak dapat dilakukan.'
                );
        }

        try {

            log_message(
                'info',
                'TRIAL_INSTALL_REQUEST_ENTERED'
            );

            /*
             * Request Trial License automatically.
             *
             * Installation UUID is handled internally
             * by TrialBootstrapClient.
             */
            $client = new TrialBootstrapClient();

            $credential = $client->requestTrial();

            /*
             * License Server decision.
             *
             * NEW_TRIAL
             * → continue automatic installation.
             *
             * EXISTING_TRIAL
             * → installation already has Trial.
             *
             * EXISTING_FULL
             * → installation already has Full.
             */
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
                            . 'lisensi Trial. Silakan Upgrade ke Full '
                            . 'atau hentikan instalasi.'
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
                            . 'terdaftar menggunakan lisensi Full. '
                            . 'Proses instalasi dihentikan.'
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

            /*
             * NEW_TRIAL.
             *
             * Continue automatic Trial installation.
             */
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

            /*
             * Validate Trial response.
             */
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

            /*
             * Local license runtime.
             */
            $runtime = new LicenseRuntimeModel();

            /*
             * Store API credential encrypted.
             */
            $store = new LicenseCredentialStore(
                $runtime,
                new LicenseCrypto()
            );

            $store->store(
                $apiKey,
                $apiSecret
            );

            /*
             * Store Trial License Key locally.
             */
            $service = new LicenseService(
                runtime: $runtime
            );

            $service->storeLicense(
                $licenseKey
            );

            /*
             * Activate Trial.
             */
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

            /*
             * Validate Trial after activation.
             */
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

            log_message(
                'info',
                'TRIAL_INSTALL_COMPLETED'
            );

            return redirect()
                ->to('/auth/login')
                ->with(
                    'success',
                    'Lisensi Trial berhasil diperoleh dan diaktifkan. Silakan login.'
                );

        } catch (\Throwable $e) {

            log_message(
                'error',
                'TRIAL_INSTALL_ERROR='
                . $e->getMessage()
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
     * Trial POST is intentionally blocked when an identity exists.
     */
    public function start(): ResponseInterface
    {
        $guard = $this->guard();

        if (!$guard->isAllowed()) {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    $guard->notice()
                    ?? 'Instalasi Trial tidak dapat dilakukan.'
                );
        }

        return redirect()
            ->to('/trial/install')
            ->with(
                'success',
                'Server siap untuk proses instalasi Trial.'
            );
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
