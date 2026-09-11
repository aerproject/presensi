<?php

declare(strict_types=1);

namespace App\Filters;

use App\Models\LicenseRuntimeModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class LicenseFilter implements FilterInterface
{
    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        $runtimeModel = new LicenseRuntimeModel();

        $runtime = $runtimeModel->getRuntime();

        if (!is_array($runtime)) {
            return $this->blocked(
                'LICENSE_RUNTIME_NOT_FOUND'
            );
        }

        $activateStatus = strtolower(
            trim((string) ($runtime['activate_status'] ?? ''))
        );

        $validateStatus = strtolower(
            trim((string) ($runtime['validate_status'] ?? ''))
        );

        if ($activateStatus !== 'active') {
            return $this->blocked(
                'LICENSE_NOT_ACTIVE'
            );
        }

        if ($validateStatus !== 'valid') {
            return $this->blocked(
                'LICENSE_NOT_VALID'
            );
        }

        $expiresAt = trim(
            (string) ($runtime['expires_at'] ?? '')
        );

        if ($expiresAt === '') {
            return $this->blocked(
                'LICENSE_EXPIRY_NOT_AVAILABLE'
            );
        }

        $expiresTimestamp = strtotime($expiresAt);

        if (
            $expiresTimestamp === false
            || $expiresTimestamp < time()
        ) {
            return $this->blocked(
                'LICENSE_EXPIRED'
            );
        }

        $serverHash = trim(
            (string) ($runtime['server_hash'] ?? '')
        );

        if ($serverHash === '') {
            return $this->blocked(
                'LICENSE_SERVER_HASH_MISSING'
            );
        }

        $storedUuid = strtolower(
            trim((string) ($runtime['server_uuid'] ?? ''))
        );

        if ($storedUuid === '') {
            return $this->blocked(
                'LICENSE_SERVER_UUID_MISSING'
            );
        }

        /*
         * Enforcement must remain read-only.
         *
         * Do not call ServerIdentity::serverUuid() here because
         * that method may persist a newly discovered identity.
         * Runtime enforcement must never mutate license_runtime.
         */

        $machineId = strtolower(
            trim((string) @file_get_contents('/etc/machine-id'))
        );

        if (
            $machineId === ''
            || !preg_match(
                '/^[a-f0-9]{32}$/',
                $machineId
            )
        ) {
            return $this->blocked(
                'LICENSE_MACHINE_ID_UNAVAILABLE'
            );
        }

        if (!hash_equals($storedUuid, $machineId)) {
            return $this->blocked(
                'LICENSE_MACHINE_MISMATCH'
            );
        }

        return null;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        return $response;
    }

    private function blocked(string $reason): ResponseInterface
    {
        return service('response')
            ->setStatusCode(403)
            ->setJSON([
                'success' => false,
                'error' => 'LICENSE_REQUIRED',
                'reason' => $reason,
                'message' => 'Aplikasi tidak dapat digunakan karena lisensi tidak valid.',
            ]);
    }
}
