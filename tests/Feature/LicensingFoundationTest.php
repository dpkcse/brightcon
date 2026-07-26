<?php

namespace Tests\Feature;

use App\Enums\LicenseStatus;
use App\Http\Middleware\EnsureLicenseEntitlement;
use App\Licensing\Exceptions\LicenseProviderException;
use App\Licensing\LicenseManager;
use App\Licensing\ProviderRegistry;
use App\Models\LicenseActivation;
use App\Services\Licensing\LicensePolicyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class LicensingFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unimplemented_provider_is_declared_but_fails_closed(): void
    {
        $this->assertFalse(config('licensing.providers.gumroad.operational'));
        $this->expectException(LicenseProviderException::class);
        $this->expectExceptionMessage('not operational');

        app(ProviderRegistry::class)->provider('gumroad');
    }

    public function test_offline_adapter_verifies_signature_product_host_and_expiry(): void
    {
        [$publicKey, $secretKey] = $this->keyPair();
        config()->set('licensing.offline.public_key', $this->encode($publicKey));

        $payload = json_encode([
            'license_id' => 'license-123',
            'product' => config('licensing.product_id'),
            'hosts' => ['example.com'],
            'expires_at' => now()->addDay()->toAtomString(),
            'entitlements' => ['updates'],
        ], JSON_THROW_ON_ERROR);
        openssl_sign($payload, $signature, $secretKey, OPENSSL_ALGO_SHA256);
        $token = $this->encode($payload).'.'.$this->encode($signature);

        $decision = app(LicenseManager::class)->activate('offline', $token, 'EXAMPLE.COM.');

        $this->assertTrue($decision->permitsUse());
        $this->assertSame(LicenseStatus::Active, $decision->status);
        $this->assertTrue(app(LicenseManager::class)->permitsUse());
        $activation = LicenseActivation::firstOrFail();
        $this->assertSame('offline', $activation->provider);
        $this->assertNotSame($token, $activation->credential_hash);
        $this->assertStringNotContainsString('example.com', $activation->host_hash);
        $this->assertSame(['entitlements' => ['updates']], $activation->provider_data);
    }

    public function test_tampered_offline_token_is_rejected_and_recorded_without_secret(): void
    {
        [$publicKey] = $this->keyPair();
        config()->set('licensing.offline.public_key', $this->encode($publicKey));
        $token = $this->encode('{"product":"buildora-cms"}').'.'.$this->encode(str_repeat('x', 64));

        $decision = app(LicenseManager::class)->activate('offline', $token, 'example.com');

        $this->assertSame(LicenseStatus::Invalid, $decision->status);
        $this->assertFalse(app(LicenseManager::class)->permitsUse());
        $this->assertDatabaseMissing('license_activations', ['credential_hash' => $token]);
    }

    public function test_unlicensed_public_site_is_not_locked_and_installer_is_independent(): void
    {
        $this->get('/')->assertOk();
        $this->get('/services')->assertOk();
        $this->get('/projects')->assertOk();
        $this->get('/gallery')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/install')->assertOk();
        $this->assertTrue(app(LicensePolicyService::class)->publicSiteAllowed());
        $this->assertTrue(app(LicensePolicyService::class)->backupAllowed());
        $this->assertTrue(app(LicensePolicyService::class)->exportAllowed());
        $this->assertFalse(app(LicensePolicyService::class)->updatesAllowed());
    }

    public function test_entitlement_middleware_denies_only_named_entitlement_with_safe_json(): void
    {
        $response = app(EnsureLicenseEntitlement::class)->handle(
            Request::create('/updates', 'GET', server: ['HTTP_ACCEPT' => 'application/json']),
            fn () => response('allowed'),
            'updates.download',
        );

        $this->assertSame(403, $response->status());
        $this->assertSame('updates.download', $response->getData(true)['action']);
        $allowed = app(EnsureLicenseEntitlement::class)->handle(Request::create('/backup'), fn () => response('allowed'), 'backup.export');
        $this->assertSame(200, $allowed->status());
    }

    public function test_unavailable_default_adapter_has_distinct_safe_state_without_fallback(): void
    {
        config()->set('licensing.default_provider', 'gumroad');

        $this->assertSame(LicenseStatus::AdapterUnavailable, app(LicenseManager::class)->status());
        $this->assertTrue(app(LicensePolicyService::class)->adminContentAllowed());
        $this->assertFalse(app(LicensePolicyService::class)->updatesAllowed());
        $this->assertSame(LicenseStatus::AdapterUnavailable, app(LicenseManager::class)->activate('gumroad', 'secret-token', 'example.com')->status);
        $this->assertDatabaseCount('license_activations', 0);
    }

    public function test_unreadable_encrypted_metadata_enters_recovery_without_erasure(): void
    {
        LicenseActivation::query()->insert([
            'installation_id' => app(LicenseManager::class)->installationId(),
            'provider' => 'offline', 'status' => LicenseStatus::Active->value,
            'credential_hash' => str_repeat('a', 64), 'host_hash' => str_repeat('b', 64),
            'provider_data' => 'preserved-unreadable-ciphertext',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->assertSame(LicenseStatus::RecoveryRequired, app(LicenseManager::class)->status());
        $this->assertTrue(app(LicensePolicyService::class)->publicSiteAllowed());
        $this->assertTrue(app(LicensePolicyService::class)->licenseManagementAllowed());
        $this->assertSame('preserved-unreadable-ciphertext', LicenseActivation::query()->toBase()->value('provider_data'));
    }

    private function encode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function keyPair(): array
    {
        $private = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($private, $secretKey);
        $publicKey = openssl_pkey_get_details($private)['key'];

        return [$publicKey, $secretKey];
    }
}
