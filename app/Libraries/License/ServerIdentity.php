<?php

declare(strict_types=1);

namespace App\Libraries\License;

use App\Libraries\Installer\InstallationIdentity;
use App\Models\LicenseRuntimeModel;
use Config\License;
use RuntimeException;

final class ServerIdentity
{
    private LicenseRuntimeModel $runtime;

    private License $config;

    public function __construct(
        ?LicenseRuntimeModel $runtime = null,
        ?License $config = null
    ) {
        $this->runtime = $runtime
            ?? new LicenseRuntimeModel();

        $this->config = $config
            ?? config('License');
    }

    /**
     * Return stable UUID for this application installation.
     *
     * IMPORTANT:
     *
     * server_uuid identifies one PRESENSI installation,
     * NOT the physical/virtual host machine.
     *
     * This allows multiple PRESENSI installations to run
     * independently on the same server.
     *
     * UUID is generated once and persisted in
     * license_runtime.id = 1.
     */
    public function serverUuid(): string
    {
        /*
         * InstallationIdentity is the canonical
         * identity source for this PRESENSI installation.
         *
         * The UUID is persisted outside the database,
         * so it remains stable even when license_runtime
         * is recreated or restored.
         */
        $installationIdentity =
            new InstallationIdentity();

        $installationUuid =
            $installationIdentity->uuid();

        /*
         * Keep license_runtime.server_uuid synchronized
         * for backward compatibility with the existing
         * LicenseService, validation, heartbeat,
         * renewal and admin components.
         */
        $runtime = $this->runtime->ensureRuntime();

        $currentServerUuid = trim(
            (string) (
                $runtime['server_uuid'] ?? ''
            )
        );

        if (
            !hash_equals(
                strtolower($installationUuid),
                strtolower($currentServerUuid)
            )
        ) {
            $updated = $this->runtime->updateRuntime([
                'server_uuid' =>
                    $installationUuid,

                'app_version' =>
                    $this->appVersion(),
            ]);

            if (!$updated) {
                throw new RuntimeException(
                    'Unable to synchronize Installation UUID.'
                );
            }
        }

        return $installationUuid;
    }

    /**
     * Return canonical identity payload for this
     * PRESENSI installation.
     *
     * server_uuid:
     *     Unique application installation identity.
     *
     * host_machine_id:
     *     Physical/virtual host identity.
     *
     * installation_fingerprint:
     *     Stable identity used by License Server to
     *     detect Trial reuse.
     *
     * @return array<string,mixed>
     */
    public function get(): array
    {
        $hostMachineId = null;

        try {
            $hostMachineId = $this->hostMachineId();
        } catch (RuntimeException) {
            // Host machine identity is metadata only.
            // Installation UUID remains the primary installation identity.
        }

        $domain = $this->domain();

        $appCode = $this->appCode();

        return [
            'server_uuid' => $this->serverUuid(),

            'host_machine_id' => $hostMachineId,

            'hostname' => php_uname('n'),

            'domain' => $domain,

            'ip_address' => $this->ipAddress(),

            'mac_address' => $this->macAddress(),

            'os_name' => PHP_OS_FAMILY
                . ' '
                . php_uname('r'),

            'php_version' => PHP_VERSION,

            'app_code' => $appCode,

            'app_version' => $this->appVersion(),

            'installation_fingerprint' =>
                $hostMachineId !== null
                    ? $this->installationFingerprint(
                        $hostMachineId,
                        $domain,
                        $appCode
                    )
                    : null,
        ];
    }

    /**
     * Return stable host machine identity.
     *
     * This identifies the physical or virtual machine.
     *
     * IMPORTANT:
     * Multiple applications on the same host will have
     * the same host_machine_id.
     */
    public function hostMachineId(): string
    {
        $machineIdPath = '/etc/machine-id';

        $machineId = trim(
            (string) @file_get_contents(
                $machineIdPath
            )
        );

        if (
            $machineId === ''
            || !preg_match(
                '/^[a-f0-9]{32}$/i',
                $machineId
            )
        ) {
            throw new RuntimeException(
                'Host machine identity could not be resolved.'
            );
        }

        return strtolower($machineId);
    }

    /**
     * Return deterministic installation fingerprint.
     *
     * The fingerprint intentionally includes:
     *
     * - host machine
     * - application domain
     * - application code
     *
     * Therefore:
     *
     * Same server + different domain
     * = different installation fingerprint
     *
     * Same server + same domain
     * = same installation fingerprint
     *
     * This allows the License Server to detect
     * Trial reuse after reinstall.
     */
    public function fingerprint(): string
    {
        return $this->installationFingerprint(
            $this->hostMachineId(),
            $this->domain(),
            $this->appCode()
        );
    }

    /**
     * Build deterministic installation fingerprint.
     */
    private function installationFingerprint(
        string $hostMachineId,
        ?string $domain,
        string $appCode
    ): string {
        return hash(
            'sha256',
            implode(
                '|',
                [
                    strtolower(
                        trim($hostMachineId)
                    ),
                    strtolower(
                        trim((string) $domain)
                    ),
                    strtoupper(
                        trim($appCode)
                    ),
                ]
            )
        );
    }

    /**
     * Compare external installation identity with
     * the current installation.
     *
     * Primary comparison uses installation fingerprint.
     */
    public function compare(array $identity): bool
    {
        $externalFingerprint = trim(
            (string) (
                $identity['installation_fingerprint']
                ?? ''
            )
        );

        if ($externalFingerprint !== '') {
            return hash_equals(
                $this->fingerprint(),
                $externalFingerprint
            );
        }

        /*
         * Backward compatibility.
         *
         * Older License Server payloads may only contain
         * server_uuid.
         */
        return hash_equals(
            $this->serverUuid(),
            (string) (
                $identity['server_uuid']
                ?? ''
            )
        );
    }

    /**
     * Return identity integrity status.
     *
     * @return array<string,mixed>
     */
    public function integrity(
        ?array $identity = null
    ): array {
        if (!is_array($identity)) {
            return [
                'status' => 'unavailable',
                'risk_level' => 'high',
            ];
        }

        $uuidMatch = false;

        $fingerprintMatch = false;

        $externalUuid = trim(
            (string) (
                $identity['server_uuid']
                ?? ''
            )
        );

        if ($externalUuid !== '') {
            $uuidMatch = hash_equals(
                $this->serverUuid(),
                $externalUuid
            );
        }

        $externalFingerprint = trim(
            (string) (
                $identity['installation_fingerprint']
                ?? ''
            )
        );

        if ($externalFingerprint !== '') {
            $fingerprintMatch = hash_equals(
                $this->fingerprint(),
                $externalFingerprint
            );
        }

        if (
            !$uuidMatch
            && !$fingerprintMatch
        ) {
            return [
                'status' => 'mismatch',
                'risk_level' => 'high',
                'uuid_match' => false,
                'fingerprint_match' => false,
            ];
        }

        if (
            $fingerprintMatch
            && !$uuidMatch
        ) {
            return [
                'status' => 'warning',
                'risk_level' => 'medium',
                'uuid_match' => false,
                'fingerprint_match' => true,
            ];
        }

        return [
            'status' => 'verified',
            'risk_level' => 'low',
            'uuid_match' => $uuidMatch,
            'fingerprint_match' => $fingerprintMatch,
        ];
    }

    /**
     * Return identity audit metadata.
     *
     * @return array<string,mixed>
     */
    public function audit(): array
    {
        $identity = $this->get();

        return [
            'server_uuid' =>
                $identity['server_uuid'] ?? null,

            'host_machine_id' =>
                $identity['host_machine_id'] ?? null,

            'domain' =>
                $identity['domain'] ?? null,

            'installation_fingerprint' =>
                $identity[
                    'installation_fingerprint'
                ] ?? null,

            'status' => 'available',

            'checked_at' =>
                date('Y-m-d H:i:s'),
        ];
    }


    /**
     * Return application domain.
     */
    private function domain(): ?string
    {
        $baseUrl = trim(
            (string) env(
                'app.baseURL',
                ''
            )
        );

        if ($baseUrl === '') {
            return null;
        }

        $host = parse_url(
            $baseUrl,
            PHP_URL_HOST
        );

        return (
            $host !== false
            && $host !== null
        )
            ? strtolower(
                (string) $host
            )
            : null;
    }

    /**
     * Return configured server IP when available.
     */
    private function ipAddress(): ?string
    {
        $configured = trim(
            (string) env(
                'LICENSE_SERVER_IP',
                ''
            )
        );

        if ($configured !== '') {
            return $configured;
        }

        if (!function_exists('socket_create')) {
            return null;
        }

        $socket = @socket_create(
            AF_INET,
            SOCK_DGRAM,
            SOL_UDP
        );

        if ($socket === false) {
            return null;
        }

        @socket_connect(
            $socket,
            '1.1.1.1',
            80
        );

        $ip = null;

        @socket_getsockname(
            $socket,
            $ip
        );

        @socket_close($socket);

        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4
        ) !== false
            ? $ip
            : null;
    }

    /**
     * Return first available network MAC address.
     */
    private function macAddress(): ?string
    {
        $interfaces = @glob(
            '/sys/class/net/*/address'
        );

        if (!is_array($interfaces)) {
            return null;
        }

        foreach ($interfaces as $file) {

            $interface = basename(
                dirname($file)
            );

            if ($interface === 'lo') {
                continue;
            }

            $mac = strtolower(
                trim(
                    (string) @file_get_contents(
                        $file
                    )
                )
            );

            if (
                $mac !== ''
                && $mac !==
                    '00:00:00:00:00:00'
            ) {
                return $mac;
            }
        }

        return null;
    }

    /**
     * Return application code.
     */
    private function appCode(): string
    {
        return strtoupper(
            trim(
                (string) env(
                    'LICENSE_APP_CODE',
                    'PRESENSI'
                )
            )
        );
    }

    /**
     * Return canonical application version.
     *
     * The version source is Config\License.
     */
    private function appVersion(): string
    {
        return $this->config->appVersion;
    }
}
