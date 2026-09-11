<?php

declare(strict_types=1);

namespace App\Libraries\License;

use App\Models\LicenseRuntimeModel;
use DateTimeImmutable;
use RuntimeException;

final class LicenseState
{
    private ?array $runtime = null;

    public function __construct(
        private readonly LicenseRuntimeModel $runtimeModel
    ) {
    }

    /**
     * Return the canonical local license runtime state.
     *
     * Read-only: this class never updates license_runtime.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->runtime();
    }

    /**
     * Return TRIAL or FULL when known.
     */
    public function type(): ?string
    {
        $value = strtolower(
            trim(
                (string) ($this->runtime()['license_type'] ?? '')
            )
        );

        return $value !== ''
            ? $value
            : null;
    }

    public function isTrial(): bool
    {
        return $this->type() === 'trial';
    }

    public function isFull(): bool
    {
        return $this->type() === 'full';
    }

    /**
     * Return local validation state.
     *
     * This is intentionally based on validate_status,
     * not on license_type or expiry.
     */
    public function status(): ?string
    {
        $value = strtolower(
            trim(
                (string) ($this->runtime()['validate_status'] ?? '')
            )
        );

        return $value !== ''
            ? $value
            : null;
    }

    public function isValid(): bool
    {
        return $this->status() === 'valid';
    }

    /**
     * Return normalized local license condition.
     *
     * This is a presentation/state accessor only.
     * Enforcement remains the responsibility of LicenseFilter.
     */
    public function condition(): string
    {
        $runtime = $this->runtime();

        if (!array_key_exists('activate_status', $runtime)) {
            return 'unavailable';
        }

        $activateStatus = strtolower(
            trim(
                (string) $runtime['activate_status']
            )
        );

        if ($activateStatus !== 'active') {
            return 'not_active';
        }

        if (!array_key_exists('validate_status', $runtime)) {
            return 'unavailable';
        }

        $validateStatus = strtolower(
            trim(
                (string) $runtime['validate_status']
            )
        );

        if ($validateStatus !== 'valid') {
            return 'not_valid';
        }

        $expiresAt = $this->expiresAt();

        if ($expiresAt === null) {
            return 'unavailable';
        }

        if ($expiresAt->getTimestamp() < time()) {
            return 'expired';
        }

        return 'valid';
    }

    /**
     * Return normalized local expiry.
     */
    public function expiresAt(): ?DateTimeImmutable
    {
        $value = trim(
            (string) ($this->runtime()['expires_at'] ?? '')
        );

        if ($value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Return remaining whole days until expiry.
     *
     * Null means expiry is unavailable or invalid.
     */
    public function daysRemaining(): ?int
    {
        $expiresAt = $this->expiresAt();

        if ($expiresAt === null) {
            return null;
        }

        $now = new DateTimeImmutable();

        $seconds = $expiresAt->getTimestamp()
            - $now->getTimestamp();

        return (int) floor(
            $seconds / 86400
        );
    }

    /**
     * Return canonical runtime row.
     *
     * @return array<string, mixed>
     */
    private function runtime(): array
    {
        if ($this->runtime === null) {
            $runtime = $this->runtimeModel->getRuntime();

            if (!is_array($runtime)) {
                throw new RuntimeException(
                    'License runtime is not available.'
                );
            }

            $this->runtime = $runtime;
        }

        return $this->runtime;
    }
}
