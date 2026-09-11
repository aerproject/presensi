<?php

declare(strict_types=1);

namespace App\Libraries\License;

use App\Models\LicenseRuntimeModel;

final class TrialReinstallGuard
{
    public const REASON_SERVER_ALREADY_REGISTERED =
        'SERVER_ALREADY_REGISTERED';

    public function __construct(
        private readonly LicenseRuntimeModel $runtime
    ) {
    }

    /**
     * Trial is allowed only when this installation
     * has no persisted Server UUID yet.
     *
     * This guard is intentionally for Trial installation only.
     * Full upgrade is not blocked by this class.
     */
    public function isAllowed(): bool
    {
        $row = $this->runtime->getRuntime();

        if (!is_array($row)) {
            return true;
        }

        return trim(
            (string) ($row['server_uuid'] ?? '')
        ) === '';
    }

    public function reason(): ?string
    {
        return $this->isAllowed()
            ? null
            : self::REASON_SERVER_ALREADY_REGISTERED;
    }

    public function notice(): ?string
    {
        if ($this->isAllowed()) {
            return null;
        }

        return
            'Instalasi Trial tidak dapat digunakan pada server ini '
            . 'karena Server UUID sudah terdaftar. '
            . 'Untuk melanjutkan instalasi pada server yang sama, '
            . 'silakan upgrade lisensi ke Full.';
    }
}
