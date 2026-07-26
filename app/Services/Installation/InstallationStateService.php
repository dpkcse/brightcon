<?php

namespace App\Services\Installation;

use App\Contracts\InstallationStateInterface;
use App\Enums\InstallationState;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class InstallationStateService implements InstallationStateInterface
{
    public function markerPath(): string
    {
        return storage_path('app/.installed');
    }

    public function isInstalled(): bool
    {
        return in_array($this->state(), [InstallationState::Installed, InstallationState::LegacyInstalled], true);
    }

    public function canRunInstaller(): bool
    {
        return in_array($this->state(), [InstallationState::Uninstalled, InstallationState::EnvironmentReady, InstallationState::MigrationsPending, InstallationState::PartiallyInstalled], true);
    }

    public function state(): InstallationState
    {
        $marker = is_file($this->markerPath());

        try {
            $hasMigrations = Schema::hasTable('migrations');
            $hasCore = Schema::hasTable('site_settings') && Schema::hasTable('users');
            $settings = $hasCore ? SiteSetting::query()->first() : null;
            $admin = $hasCore && Schema::hasColumn('users', 'is_admin') && DB::table('users')->where('is_admin', true)->exists();
            $databaseComplete = filled($settings?->installation_completed_at) && filled($settings?->installed_version) && $admin;
            $legacy = $hasCore && $settings && $admin && $hasMigrations;

            if ($marker && $databaseComplete) {
                return InstallationState::Installed;
            }
            if ($marker || ($databaseComplete && ! $marker)) {
                return InstallationState::Inconsistent;
            }
            if ($legacy) {
                return InstallationState::LegacyInstalled;
            }
            if ($hasCore || $hasMigrations) {
                return InstallationState::PartiallyInstalled;
            }
        } catch (Throwable) {
            // A durable marker means an outage is never treated as a fresh installation.
            if ($marker) {
                return InstallationState::Inconsistent;
            }

            return is_file(storage_path('app/.installation-partial'))
                ? InstallationState::EnvironmentReady
                : (is_file(base_path('.env')) ? InstallationState::Inconsistent : InstallationState::Uninstalled);
        }

        return is_file(base_path('.env')) ? InstallationState::EnvironmentReady : InstallationState::Uninstalled;
    }

    public function markInstalled(): void
    {
        $directory = dirname($this->markerPath());
        if (! is_dir($directory) || ! is_writable($directory)) {
            throw new RuntimeException('The installation marker directory is not writable.');
        }
        $payload = json_encode(['installed_at' => now()->toIso8601String(), 'version' => config('cms.product.version'), 'installation_id' => (string) Str::uuid()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        $temporary = $this->markerPath().'.'.Str::random(12).'.tmp';
        if (file_put_contents($temporary, $payload, LOCK_EX) === false || ! rename($temporary, $this->markerPath())) {
            @unlink($temporary);
            throw new RuntimeException('The installation marker could not be written.');
        }
        @chmod($this->markerPath(), 0600);
        @unlink(storage_path('app/.installer-key'));
        @unlink(storage_path('app/.installation-partial'));
    }

    public function diagnostics(): array
    {
        return ['state' => $this->state()->value, 'marker_present' => is_file($this->markerPath()), 'environment_present' => is_file(base_path('.env')), 'installer_allowed' => $this->canRunInstaller()];
    }
}
