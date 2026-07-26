<?php

namespace App\Licensing;

use App\Enums\LicenseStatus;
use App\Licensing\Data\ActivationRequest;
use App\Licensing\Data\LicenseDecision;
use App\Licensing\Exceptions\LicenseProviderException;
use App\Models\LicenseActivation;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class LicenseManager
{
    public function __construct(private ProviderRegistry $providers) {}

    public function activate(string $provider, string $credential, string $host): LicenseDecision
    {
        $installationId = $this->installationId();
        try {
            $decision = $this->providers->provider($provider)->activate(new ActivationRequest(
                $credential, $installationId, $host, (string) config('licensing.product_id'),
            ));
        } catch (LicenseProviderException) {
            return new LicenseDecision(LicenseStatus::AdapterUnavailable, reason: 'The selected license provider is unavailable.');
        }

        DB::transaction(function () use ($provider, $credential, $host, $installationId, $decision): void {
            LicenseActivation::query()->updateOrCreate(
                ['installation_id' => $installationId],
                [
                    'provider' => $provider,
                    'status' => $decision->status,
                    'external_reference' => $decision->externalReference ?: null,
                    'credential_hash' => hash_hmac('sha256', $credential, $this->hashKey()),
                    'host_hash' => hash_hmac('sha256', $this->normalizeHost($host), $this->hashKey()),
                    'provider_data' => $decision->metadata,
                    'verified_at' => now(),
                    'expires_at' => $decision->expiresAt,
                ],
            );
        });

        return $decision;
    }

    public function current(): ?LicenseActivation
    {
        return LicenseActivation::query()->where('installation_id', $this->installationId())->first();
    }

    public function permitsUse(): bool
    {
        $activation = $this->current();

        return $activation !== null
            && $activation->status === LicenseStatus::Active
            && ($activation->expires_at === null || $activation->expires_at->isFuture());
    }

    public function status(): LicenseStatus
    {
        $activation = $this->current();
        if ($activation === null) {
            $configured = (string) config('licensing.default_provider');
            $definition = config("licensing.providers.{$configured}");

            return is_array($definition) && ! ($definition['operational'] ?? false)
                ? LicenseStatus::AdapterUnavailable : LicenseStatus::Inactive;
        }

        try {
            // Force encrypted metadata recovery to be checked without changing it.
            $activation->provider_data;
        } catch (DecryptException) {
            return LicenseStatus::RecoveryRequired;
        }

        return $activation->status === LicenseStatus::Active && $activation->expires_at?->isPast()
            ? LicenseStatus::Expired : $activation->status;
    }

    public function installationId(): string
    {
        $path = storage_path('app/.license-installation-id');
        $existing = is_file($path) ? trim((string) file_get_contents($path)) : '';
        if (Str::isUuid($existing)) {
            return $existing;
        }

        $id = (string) Str::uuid();
        if (! is_dir(dirname($path)) || file_put_contents($path, $id, LOCK_EX) === false) {
            throw new \RuntimeException('The license installation identifier could not be persisted.');
        }
        @chmod($path, 0600);

        return $id;
    }

    private function hashKey(): string
    {
        return hash('sha256', (string) config('app.key'), true);
    }

    private function normalizeHost(string $host): string
    {
        return strtolower(rtrim(trim($host), '.'));
    }
}
