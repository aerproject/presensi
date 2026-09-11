<?php

declare(strict_types=1);

namespace App\Libraries\License;

use App\Models\LicenseRuntimeModel;

final class ServerIdentity
{
    private LicenseRuntimeModel $runtime;

    public function __construct(
        ?LicenseRuntimeModel $runtime = null
    ) {
        $this->runtime = $runtime ?? new LicenseRuntimeModel();
    }

    /**
     * Return stable installation identity.
     *
     * server_uuid is generated exactly once and persisted
     * in license_runtime.id = 1.
     */
    public function serverUuid(): string
    {
        $row = $this->runtime->ensureRuntime();

        /*
        |--------------------------------------------------------------------------
        | Existing Canonical Identity
        |--------------------------------------------------------------------------
        |
        | Once an AER server identity has been persisted, it is immutable.
        | This prevents application reinstall or configuration changes from
        | generating a different identity.
        |
        */
        $existing = trim(
            (string) ($row['server_uuid'] ?? '')
        );

        if ($existing !== '') {
            return $existing;
        }

        /*
        |--------------------------------------------------------------------------
        | Canonical Machine Identity
        |--------------------------------------------------------------------------
        |
        | AER server_uuid represents the physical/virtual server host,
        | not the application installation.
        |
        | /etc/machine-id survives application uninstall/reinstall and is
        | readable by the PHP-FPM user.
        |
        */
        $machineIdPath = '/etc/machine-id';

        $machineId = trim(
            (string) @file_get_contents($machineIdPath)
        );

        if (
            $machineId === ''
            || ! preg_match(
                '/^[a-f0-9]{32}$/i',
                $machineId
            )
        ) {
            throw new \RuntimeException(
                'Canonical server identity could not be resolved.'
            );
        }

        $machineId = strtolower($machineId);

        $this->runtime->updateRuntime([
            'server_uuid' => $machineId,
            'app_version' => (string) env(
                'LICENSE_APP_VERSION',
                '1.0.0'
            ),
        ]);

        return $machineId;
    }

    /**
     * Return canonical server identity payload.
     *
     * @return array<string,mixed>
     */
    public function get(): array
    {
        return [
            'server_uuid'  => $this->serverUuid(),
            'hostname'     => php_uname('n'),
            'domain'       => $this->domain(),
            'ip_address'   => $this->ipAddress(),
            'mac_address'  => $this->macAddress(),
            'os_name'      => PHP_OS_FAMILY . ' ' . php_uname('r'),
            'php_version'  => PHP_VERSION,
            'app_version'  => (string) env(
                'LICENSE_APP_VERSION',
                '1.0.0'
            ),
        ];
    }


    /**
     * Return deterministic server identity fingerprint.
     *
     * Read-only:
     * - no runtime mutation
     * - no database write
     * - no network request
     */
    public function fingerprint(): string
    {
        $identity = $this->get();

        ksort($identity);

        return hash(
            'sha256',
            json_encode(
                $identity,
                JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
            )
        );
    }


    /**
     * Compare external identity payload with current server identity.
     *
     * Read-only:
     * - no runtime mutation
     * - no database update
     * - no network request
     *
     * @param array<string,mixed> $identity
     */
    public function compare(array $identity): bool
    {
        $current = $this->get();

        return hash_equals(
            (string) ($current['server_uuid'] ?? ''),
            (string) ($identity['server_uuid'] ?? '')
        );
    }

    /**
     * Return identity integrity status.
     *
     * Read-only evaluation.
     *
     * @return array<string,mixed>
     */

    /**
     * Return identity audit metadata.
     *
     * Read-only audit representation.
     *
     * @return array<string,mixed>
     */
    public function audit(): array
    {
        $identity = $this->get();

        $fingerprint = $this->fingerprint();

        $identity['fingerprint'] = $fingerprint;

        $integrity = $this->integrity($identity);

        return [
            'server_uuid' => $identity['server_uuid'] ?? null,
            'fingerprint' => $fingerprint,
            'status' => $integrity['status'] ?? 'unavailable',
            'risk_level' => $integrity['risk_level'] ?? 'high',
            'checked_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function integrity(?array $identity = null): array
    {
        $current = $this->get();

        if (!is_array($identity)) {
            return [
                'status' => 'unavailable',
                'risk_level' => 'high',
            ];
        }

        $uuidMatch = hash_equals(
            (string) ($current['server_uuid'] ?? ''),
            (string) ($identity['server_uuid'] ?? '')
        );

        $fingerprintMatch = false;

        if (
            isset($identity['fingerprint'])
            && is_string($identity['fingerprint'])
        ) {
            $fingerprintMatch = hash_equals(
                $this->fingerprint(),
                $identity['fingerprint']
            );
        }

        if (!$uuidMatch) {
            return [
                'status' => 'mismatch',
                'risk_level' => 'high',
                'uuid_match' => false,
                'fingerprint_match' => $fingerprintMatch,
            ];
        }

        if (
            isset($identity['fingerprint'])
            && !$fingerprintMatch
        ) {
            return [
                'status' => 'warning',
                'risk_level' => 'medium',
                'uuid_match' => true,
                'fingerprint_match' => false,
            ];
        }

        return [
            'status' => 'verified',
            'risk_level' => 'low',
            'uuid_match' => true,
            'fingerprint_match' => true,
        ];
    }

    private function generateUuidV4(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(
            (ord($data[6]) & 0x0f) | 0x40
        );

        $data[8] = chr(
            (ord($data[8]) & 0x3f) | 0x80
        );

        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(bin2hex($data), 4)
        );
    }

    private function domain(): ?string
    {
        $baseUrl = trim(
            (string) env('app.baseURL', '')
        );

        if ($baseUrl === '') {
            return null;
        }

        $host = parse_url($baseUrl, PHP_URL_HOST);

        return $host !== false && $host !== null
            ? (string) $host
            : null;
    }

    private function ipAddress(): ?string
    {
        $configured = trim(
            (string) env('LICENSE_SERVER_IP', '')
        );

        return $configured !== ''
            ? $configured
            : null;
    }

    private function macAddress(): ?string
    {
        $interfaces = @glob('/sys/class/net/*/address');

        if (!is_array($interfaces)) {
            return null;
        }

        foreach ($interfaces as $file) {
            $interface = basename(dirname($file));

            if ($interface === 'lo') {
                continue;
            }

            $mac = strtolower(
                trim((string) @file_get_contents($file))
            );

            if (
                $mac !== ''
                && $mac !== '00:00:00:00:00:00'
            ) {
                return $mac;
            }
        }

        return null;
    }
}
