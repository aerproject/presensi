<?php

declare(strict_types=1);

namespace App\Libraries\License;

use App\Models\LicenseRuntimeModel;

final class TrialBootstrap
{
    public function __construct(
        private readonly LicenseRuntimeModel $runtime
    ) {
    }

    /**
     * Determine whether this installation may bootstrap Trial.
     *
     * Read-only.
     */
    public function isEligible(): bool
    {
        $row = $this->runtime->getRuntime();

        if (!is_array($row)) {
            return true;
        }

        $activateStatus = strtolower(
            trim((string) ($row['activate_status'] ?? ''))
        );

        $serverId = $row['server_id'] ?? null;

        return !(
            $activateStatus === 'active'
            && $serverId !== null
        );
    }

    /**
     * Return the canonical Global Trial License Key.
     *
     * This method only exposes the configured bootstrap key.
     * It does not store or activate it.
     */
    public function trialLicenseKey(): string
    {
        $key = strtoupper(
            trim(
                (string) env(
                    'LICENSE_TRIAL_KEY',
                    ''
                )
            )
        );

        if ($key === '') {
            throw new \RuntimeException(
                'LICENSE_TRIAL_KEY is required.'
            );
        }

        return $key;
    }
}
