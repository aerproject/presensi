<?php

declare(strict_types=1);

namespace App\Controllers;

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

    public function index(): string
    {
        $guard = $this->guard();

        $runtime = new LicenseRuntimeModel();

        $row = $runtime->getRuntime();

        $credentialStore = new LicenseCredentialStore(
            $runtime,
            new LicenseCrypto()
        );

        $serverUuid = trim(
            (string) ($row['server_uuid'] ?? '')
        );

        return view('trial/install', [
            'allowed' => $guard->isAllowed(),
            'reason' => $guard->reason(),
            'notice' => $guard->notice(),

            'serverUuidExists' => $serverUuid !== '',

            'credentialReady' =>
                $credentialStore->has(),

            'runtime' => $row,
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
     * Full upgrade entry point.
     *
     * Existing Server UUID is intentionally preserved.
     * No new identity is generated here.
     */
    public function upgrade(): string
    {
        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        $serverUuid = trim(
            (string) ($row['server_uuid'] ?? '')
        );

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
            'serverUuid' => $serverUuid,
            'serverUuidExists' => $serverUuid !== '',
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

        $serverUuid = trim(
            (string) ($row['server_uuid'] ?? '')
        );

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
            'serverUuid' => $serverUuid,
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

        $serverUuid = trim(
            (string) ($row['server_uuid'] ?? '')
        );

        return view('trial/upgrade-status', [
            'serverUuid' => $serverUuid,
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

        $serverUuid = trim(
            (string) ($row['server_uuid'] ?? '')
        );

        if ($serverUuid === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->with(
                    'error',
                    'Upgrade Full hanya dapat dilakukan pada server yang sudah memiliki Server UUID.'
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

        $serverUuid = trim(
            (string) ($row['server_uuid'] ?? '')
        );

        if ($serverUuid === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->with(
                    'error',
                    'Upgrade Full hanya dapat dilakukan pada server yang sudah memiliki Server UUID.'
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

        $serverUuid = trim(
            (string) ($row['server_uuid'] ?? '')
        );

        if ($serverUuid === '') {
            return redirect()
                ->to('/trial/upgrade')
                ->with(
                    'error',
                    'Server UUID belum tersedia.'
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
                ->to('/trial/install')
                ->withInput()
                ->with(
                    'error',
                    'Trial License Key wajib diisi.'
                );
        }

        if ($apiKey === '') {
            return redirect()
                ->to('/trial/install')
                ->withInput()
                ->with(
                    'error',
                    'Trial API Key wajib diisi.'
                );
        }

        try {
            $client = new TrialBootstrapClient();

            $credential = $client->bootstrap(
                $licenseKey,
                $apiKey
            );

            $runtime = new LicenseRuntimeModel();

            $store = new LicenseCredentialStore(
                $runtime,
                new LicenseCrypto()
            );

            $store->store(
                $credential['api_key'],
                $credential['api_secret']
            );

            $service = new LicenseService(
                runtime: $runtime
            );

            $service->storeLicense(
                $licenseKey
            );

            $result = $service->activate(
                $licenseKey
            );

            if (
                !is_array($result)
                ||
                ($result['success'] ?? false) !== true
            ) {
                throw new \RuntimeException(
                    'Trial activation failed.'
                );
            }

            return redirect()
                ->to('/auth/login')
                ->with(
                    'success',
                    'Trial berhasil diaktifkan. Silakan login.'
                );

        } catch (\Throwable $e) {
            return redirect()
                ->to('/trial/install')
                ->withInput()
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

        $serverUuid = trim(
            (string) ($row['server_uuid'] ?? '')
        );

        if ($serverUuid === '') {
            return redirect()
                ->to('/trial/install')
                ->with(
                    'error',
                    'Server UUID belum tersedia.'
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
