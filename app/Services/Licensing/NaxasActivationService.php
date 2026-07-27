<?php

namespace App\Services\Licensing;

use App\Licensing\LicenseManager;
use App\Models\LicenseActivationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final class NaxasActivationService
{
    public function __construct(private NaxasPortalClient $client, private LicenseManager $licenses) {}

    public function current(): ?LicenseActivationRequest
    {
        return LicenseActivationRequest::query()
            ->where('installation_uuid', $this->licenses->installationId())
            ->where('provider', 'naxas_portal')->latest('id')->first();
    }

    public function create(): LicenseActivationRequest
    {
        $this->ensureEnabled();
        $installation = $this->licenses->installationId();
        $domain = $this->domain();
        $active = LicenseActivationRequest::query()->where([
            'installation_uuid' => $installation, 'provider' => 'naxas_portal', 'normalized_domain' => $domain,
        ])->whereIn('status', ['pending', 'approved'])->where('expires_at', '>', now())->first();
        if ($active !== null) {
            return $active;
        }

        LicenseActivationRequest::query()->where([
            'installation_uuid' => $installation, 'provider' => 'naxas_portal', 'normalized_domain' => $domain,
        ])->whereIn('status', ['pending', 'approved'])->update(['status' => 'expired', 'request_token_ciphertext' => null]);

        $payload = [
            'product' => (string) config('licensing.product_id'),
            'version' => (string) config('cms.product.version', config('commercial_release.default_version', '1.0.0')),
            'license_type' => 'single_site',
            'installation_uuid' => $installation,
            'domain' => $domain,
            'environment' => app()->environment('production') ? 'production' : 'non-production',
            'nonce' => Str::random(48),
        ];
        $response = $this->client->create($payload);
        $this->client->validatePortalUrl($response['portal_url']);
        $token = $response['request_token'];

        return LicenseActivationRequest::query()->create([
            'installation_uuid' => $installation,
            'provider' => 'naxas_portal',
            'remote_request_id' => $response['request_id'],
            'request_token_hash' => hash_hmac('sha256', $token, $this->hashKey()),
            'request_token_ciphertext' => $token,
            'masked_request_token' => $this->mask($token),
            'normalized_domain' => $domain,
            'product_reference' => $payload['product'],
            'application_version' => $payload['version'],
            'portal_url' => $response['portal_url'],
            'status' => 'pending',
            'requested_at' => now(),
            'expires_at' => $response['expires_at'],
        ]);
    }

    public function check(): string
    {
        $this->ensureEnabled();
        $request = $this->current();
        if ($request === null || in_array($request->status, ['completed', 'cancelled'], true)) {
            throw new RuntimeException('There is no pending activation request to check.');
        }
        if ($request->expires_at->isPast()) {
            $request->update(['status' => 'expired', 'request_token_ciphertext' => null]);
            throw new RuntimeException('The activation request has expired. Create a new request.');
        }
        if ($request->installation_uuid !== $this->licenses->installationId() || $request->normalized_domain !== $this->domain()) {
            throw new RuntimeException('The installation or production domain changed. Create a new activation request.');
        }
        $token = $request->request_token_ciphertext;
        if (! is_string($token) || ! hash_equals($request->request_token_hash, hash_hmac('sha256', $token, $this->hashKey()))) {
            $request->update(['status' => 'recovery_required']);
            throw new RuntimeException('The activation request proof is unavailable. Create a new request.');
        }

        $response = $this->client->status($request->remote_request_id, $token);
        $request->last_checked_at = now();
        if ($response['status'] === 'approved') {
            $claims = $this->untrustedClaims($response['signed_license']);
            if (($claims['installation_uuid'] ?? null) !== $request->installation_uuid || ($claims['license_type'] ?? null) !== 'single_site') {
                $request->save();
                throw new RuntimeException('The retrieved signed license does not match this installation or requested license type.');
            }
            $decision = $this->licenses->verify('offline', $response['signed_license'], $request->normalized_domain);
            if (! $decision->permitsUse()) {
                throw new RuntimeException($decision->reason ?: 'The retrieved signed license failed local verification.');
            }
            $this->licenses->activate('offline', $response['signed_license'], $request->normalized_domain);
            DB::transaction(function () use ($request): void {
                $request->update([
                    'status' => 'completed', 'approved_at' => now(), 'completed_at' => now(),
                    'request_token_ciphertext' => null, 'safe_failure_message' => null, 'failure_code' => null,
                ]);
            });

            return 'The signed license was retrieved, verified locally, and activated.';
        }

        $request->update([
            'status' => $response['status'],
            'failure_code' => $response['failure_code'] ?? null,
            'safe_failure_message' => $response['safe_message'] ?? null,
            'request_token_ciphertext' => in_array($response['status'], ['expired', 'rejected', 'completed'], true) ? null : $token,
        ]);

        return $response['safe_message'] ?? 'The activation request is still pending approval.';
    }

    private function ensureEnabled(): void
    {
        if (! config('licensing.naxas_portal.enabled')) {
            throw new RuntimeException('Naxas portal activation is pending deployment. Use manual signed-license activation.');
        }
    }

    private function domain(): string
    {
        return strtolower(rtrim((string) parse_url((string) config('app.url'), PHP_URL_HOST), '.'));
    }

    private function hashKey(): string
    {
        return hash('sha256', (string) config('app.key'), true);
    }

    private function mask(string $token): string
    {
        return strlen($token) <= 8 ? str_repeat('*', strlen($token)) : substr($token, 0, 4).str_repeat('*', min(12, strlen($token) - 8)).substr($token, -4);
    }

    private function untrustedClaims(string $signedLicense): array
    {
        $encoded = explode('.', trim($signedLicense), 2)[0] ?? '';
        $decoded = base64_decode(strtr($encoded, '-_', '+/').str_repeat('=', (4 - strlen($encoded) % 4) % 4), true);
        $claims = $decoded === false ? null : json_decode($decoded, true);

        return is_array($claims) ? $claims : [];
    }
}
