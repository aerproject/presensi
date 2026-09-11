<?php

declare(strict_types=1);

namespace App\Libraries\License;

final class LicenseTypeResolver
{
    public function resolve(
        string $licenseKey
    ): ?string {
        $licenseKey = strtoupper(
            trim($licenseKey)
        );

        if ($licenseKey === '') {
            return null;
        }

        if (
            str_starts_with(
                $licenseKey,
                'AER-TR-'
            )
        ) {
            return 'trial';
        }

        if (
            str_starts_with(
                $licenseKey,
                'AER-FL-'
            )
        ) {
            return 'full';
        }

        return null;
    }
}
