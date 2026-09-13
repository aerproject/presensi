<?php

declare(strict_types=1);

namespace App\Libraries\License;

/**
 * Local Trial installation guard.
 *
 * IMPORTANT:
 *
 * License ownership and Installation UUID registration
 * are decided by the License Server through
 * TrialBootstrapClient.
 *
 * The License Server returns one of:
 *
 * NEW_TRIAL
 * EXISTING_TRIAL
 * EXISTING_FULL
 *
 * This guard MUST NOT use local runtime identity
 * as the canonical installation identity source.
 */
final class TrialReinstallGuard
{
    /**
     * Trial request may proceed to License Server.
     *
     * Final decision is made remotely based on the
     * canonical Installation UUID.
     */
    public function isAllowed(): bool
    {
        return true;
    }

    /**
     * No local blocking reason.
     *
     * License Server provides the authoritative
     * installation decision.
     */
    public function reason(): ?string
    {
        return null;
    }

    /**
     * No local blocking notice.
     *
     * Remote License Server response determines
     * whether installation continues, upgrades,
     * or is stopped.
     */
    public function notice(): ?string
    {
        return null;
    }
}
