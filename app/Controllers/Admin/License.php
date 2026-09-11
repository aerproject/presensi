<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\License\ServerIdentity;
use App\Libraries\License\LicenseService;
use App\Models\LicenseRenewalRequestModel;
use App\Models\LicenseRuntimeModel;
use CodeIgniter\HTTP\ResponseInterface;

final class License extends BaseController
{
    public function index()
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $state = service('licenseState');

        $identity = new ServerIdentity();

        return view('admin/license/index', [
            'title' => 'Lisensi | Admin Panel',
            'license' => $state,
            'identity' => $identity->audit(),
        ]);
    }

    /**
     * Display Full license renewal request form.
     */
    public function renewForm(): string|ResponseInterface
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $state = service('licenseState');

        log_message(
            'error',
            'RENEWAL TRACE: LicenseState resolved'
        );

        log_message(
            'error',
            'RENEWAL TRACE: type='
            . (string) ($state->type() ?? 'NULL')
            . ' isFull='
            . ($state->isFull() ? 'TRUE' : 'FALSE')
        );

        if (
            ! $state->isFull()
        ) {
            return redirect()
                ->to('/admin/license');
        }

        $identity = new ServerIdentity();

        return view('admin/license/renew', [
            'title' => 'Ajukan Perpanjangan Lisensi',
            'license' => $state,
            'identity' => $identity->audit(),
        ]);
    }

    /**
     * Store a Full license renewal request.
     */
    public function submitRenewal(): ResponseInterface
    {

        if ($response = $this->requireAdmin()) {
            return $response;
        }

        log_message(
            'error',
            'RENEWAL TRACE: submitRenewal ENTER'
        );

        log_message(
            'error',
            'RENEWAL TRACE: before licenseState service'
        );

        try {
            $state = service('licenseState');

            log_message(
                'error',
                'RENEWAL TRACE: after licenseState service'
            );
        } catch (\Throwable $e) {
            log_message(
                'error',
                'RENEWAL TRACE: licenseState EXCEPTION: '
                . $e::class
                . ' - '
                . $e->getMessage()
            );

            throw $e;
        }

        if (
            ! $state->isFull()
        ) {
            return redirect()
                ->to('/admin/license')
                ->with(
                    'error',
                    'Perpanjangan hanya dapat diajukan untuk lisensi Full.'
                );
        }

        log_message(
            'error',
            'RENEWAL TRACE: licenseState is FULL'
        );

        $notes = trim(
            (string) $this->request->getPost('notes')
        );

        $userId = session()->get('user_id');

        if (
            ! is_numeric($userId)
            || (int) $userId <= 0
        ) {
            return redirect()
                ->to('/admin/license')
                ->with(
                    'error',
                    'User login tidak valid.'
                );
        }

        $runtime = new LicenseRuntimeModel();
        $row = $runtime->getRuntime();

        log_message(
            'error',
            'RENEWAL TRACE: Runtime resolved'
        );

        if (! is_array($row)) {
            return redirect()
                ->to('/admin/license')
                ->with(
                    'error',
                    'Runtime lisensi tidak ditemukan.'
                );
        }

        /*
         * Create one correlation UUID for both systems.
         */
        $bytes = random_bytes(16);

        $bytes[6] = chr(
            ord($bytes[6]) & 0x0f
                | 0x40
        );

        $bytes[8] = chr(
            ord($bytes[8]) & 0x3f
                | 0x80
        );

        $requestUuid = vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(
                bin2hex($bytes),
                4
            )
        );

        log_message(
            'error',
            'RENEWAL TRACE: request UUID generated '
            . $requestUuid
        );

        /*
         * Submit to License Server first.
         *
         * No local request is stored when the remote submission fails.
         */
        $service = new LicenseService(
            runtime: $runtime
        );

        log_message(
            'error',
            'RENEWAL TRACE: LicenseService constructed'
        );

        try {
            $remote = $service->submitRenewalRequest(
                requestUuid: $requestUuid,
                notes: $notes !== ''
                    ? $notes
                    : null
            );

            log_message(
                'error',
                'RENEWAL TRACE: remote submission OK'
            );

            log_message(
                'error',
                'RENEWAL TRACE: remote type='
                . get_debug_type($remote)
            );

            if (is_array($remote)) {
                log_message(
                    'error',
                    'RENEWAL TRACE: remote keys='
                    . implode(
                        ',',
                        array_keys($remote)
                    )
                );
            }
        } catch (\Throwable $e) {
            log_message(
                'error',
                'RENEWAL TRACE: REMOTE EXCEPTION '
                . $e::class
                . ' - '
                . $e->getMessage()
            );

            return redirect()
                ->to('/admin/license/renew')
                ->withInput()
                ->with(
                    'error',
                    'Pengajuan ke License Server gagal: '
                    . $e->getMessage()
                );
        }

        $authoritativeRequestUuid = trim(
            (string) (
                $remote['request_uuid']
                ?? $requestUuid
            )
        );

        if ($authoritativeRequestUuid === '') {
            throw new \RuntimeException(
                'License Server tidak mengembalikan request_uuid.'
            );
        }

        log_message(
            'error',
            'RENEWAL TRACE: authoritative request_uuid='
            . $authoritativeRequestUuid
        );

        $requests = new LicenseRenewalRequestModel();

        $requests->insert([
            'license_id' => $row['license_id'] ?? null,
            'application_id' => $row['application_id'] ?? null,
            'server_id' => $row['server_id'] ?? null,
            'server_uuid' => $row['server_uuid'] ?? null,
            'server_hash' => $row['server_hash'] ?? null,
            'request_uuid' => $authoritativeRequestUuid,
            'current_expires_at' => $row['expires_at'] ?? null,
            'requested_expires_at' => null,
            'notes' => $notes !== '' ? $notes : null,
            'status' => 'pending',
            'submitted_by' => (int) $userId,
            'submitted_at' => date('Y-m-d H:i:s'),
            'processed_at' => null,
            'response_message' => $remote['message'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()
            ->to('/admin/license')
            ->with(
                'success',
                'Pengajuan perpanjangan lisensi berhasil dikirim ke License Server dan menunggu persetujuan.'
            );
    }
}
