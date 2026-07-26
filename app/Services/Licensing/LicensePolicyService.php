<?php

namespace App\Services\Licensing;

use App\Enums\LicenseEnforcementLevel;
use App\Enums\LicenseStatus;
use App\Licensing\Data\LicensePolicyDecision;
use App\Licensing\LicenseManager;

final class LicensePolicyService
{
    public const ALWAYS_ALLOWED = [
        'public.view', 'admin.content.view', 'admin.content.create', 'admin.content.update',
        'admin.content.delete', 'backup.export', 'data.export', 'license.view',
        'license.activate', 'license.recover', 'system.status', 'system.recovery',
    ];

    public function __construct(private LicenseManager $licenses) {}

    public function decisionFor(string $action): LicensePolicyDecision
    {
        $status = $this->licenses->status();
        $valid = $status === LicenseStatus::Active && $this->licenses->permitsUse();
        $restricted = $action === 'updates.download' || str_starts_with($action, 'premium.');
        $allowed = in_array($action, self::ALWAYS_ALLOWED, true) || ! $restricted || $valid;

        return new LicensePolicyDecision($action, $allowed, $status, $this->enforcementLevel(), ...$this->notice($status));
    }

    public function can(string $action): bool
    {
        return $this->decisionFor($action)->allowed;
    }

    public function enforcementLevel(): LicenseEnforcementLevel
    {
        return $this->licenses->status() === LicenseStatus::Active
            ? LicenseEnforcementLevel::Informational
            : LicenseEnforcementLevel::UpdatesRestricted;
    }

    public function publicSiteAllowed(): bool
    {
        return $this->can('public.view');
    }

    public function adminContentAllowed(): bool
    {
        return $this->can('admin.content.update');
    }

    public function backupAllowed(): bool
    {
        return $this->can('backup.export');
    }

    public function exportAllowed(): bool
    {
        return $this->can('data.export');
    }

    public function licenseManagementAllowed(): bool
    {
        return $this->can('license.recover');
    }

    public function systemRecoveryAllowed(): bool
    {
        return $this->can('system.recovery');
    }

    public function updatesAllowed(): bool
    {
        return $this->can('updates.download');
    }

    public function premiumFeatureAllowed(string $feature): bool
    {
        return $this->can('premium.'.$feature);
    }

    public function safeNotice(): ?string
    {
        return $this->decisionFor('license.view')->notice;
    }

    /** @return array{0: ?string, 1: string} */
    private function notice(LicenseStatus $status): array
    {
        return match ($status) {
            LicenseStatus::Active => [null, 'information'],
            LicenseStatus::DomainMismatch => ['The license is assigned to another domain. Review the configured application URL or activate a license for this domain.', 'critical'],
            LicenseStatus::AdapterUnavailable => ['The selected license provider is not implemented in this release. Choose an operational provider or use an offline signed license.', 'warning'],
            LicenseStatus::RecoveryRequired => ['The saved license could not be decrypted after an application-key change. Enter the license again.', 'critical'],
            LicenseStatus::ProviderUnavailable => ['License verification is temporarily unavailable. The previous entitlement remains in grace; core functions are unaffected.', 'warning'],
            LicenseStatus::Expired => ['The license has expired. Core website functions remain available, but updates and commercial support entitlement are unavailable.', 'warning'],
            LicenseStatus::Invalid, LicenseStatus::WrongProduct => ['The saved license could not be verified. Enter a valid signed license; core functions and recovery remain available.', 'critical'],
            LicenseStatus::ConfigurationMissing => ['License verification is not configured. Add the offline public verification key and activate again.', 'warning'],
            default => ['Buildora CMS is not activated. Core website functions remain available, but product updates and commercial support entitlement are unavailable.', 'information'],
        };
    }
}
