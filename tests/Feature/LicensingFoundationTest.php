<?php

namespace Tests\Feature;

use App\Enums\LicenseStatus;
use App\Http\Middleware\RequireValidLicense;
use App\Licensing\Exceptions\LicenseProviderException;
use App\Licensing\LicenseManager;
use App\Licensing\ProviderRegistry;
use App\Models\LicenseActivation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    public function test_license_middleware_cannot_be_bypassed_by_demo_configuration(): void
    {
        config()->set('licensing.enforce', true);
        $this->get('/')->assertStatus(503);
        $this->get('/install')->assertStatus(200); // Installer state remains reachable and independent.

        $this->expectException(HttpException::class);
        app(RequireValidLicense::class)->handle(Request::create('/protected'), fn () => response('allowed'));
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
