<?php

namespace Tests\Feature;

use App\Licensing\LicenseManager;
use App\Models\LicenseActivationRequest;
use App\Models\User;
use App\Services\Licensing\NaxasActivationService;
use DateTimeInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NaxasPortalActivationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['installer.enforce' => false, 'app.url' => 'https://Example.COM.',
            'licensing.naxas_portal.enabled' => true, 'licensing.naxas_portal.base_url' => 'https://licenses.naxasltd.com']);
    }

    public function test_only_admin_can_create_a_safely_stored_request_with_safe_payload(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password', 'is_admin' => true]);
        $token = 'BRQ-AAAA-BBBB-CCCC';
        Http::fake(['https://licenses.naxasltd.com/*' => Http::response([
            'request_id' => 'request-1', 'request_token' => $token, 'status' => 'pending',
            'expires_at' => now()->addDay()->toAtomString(), 'portal_url' => 'https://licenses.naxasltd.com/activate',
        ])]);
        Log::spy();

        $this->post(route('admin.license.request-activation'))->assertRedirect(route('admin.login'));
        $this->actingAs($admin)->post(route('admin.license.request-activation'))->assertRedirect(route('admin.license.index'));

        Http::assertSent(function ($request): bool {
            $data = $request->data();

            return $request->url() === 'https://licenses.naxasltd.com/api/v1/activation-requests'
                && $data['product'] === 'buildora-cms' && $data['license_type'] === 'single_site'
                && $data['domain'] === 'example.com' && isset($data['installation_uuid'], $data['nonce'])
                && ! array_intersect(['app_key', 'db_password', 'admin_password', 'email', 'users'], array_keys($data));
        });
        $record = LicenseActivationRequest::firstOrFail();
        $this->assertSame($token, $record->request_token_ciphertext);
        $this->assertStringNotContainsString($token, (string) LicenseActivationRequest::query()->toBase()->value('request_token_ciphertext'));
        $this->assertNotSame($token, $record->request_token_hash);
        Log::shouldNotHaveReceived('info');
    }

    public function test_request_business_dates_are_datetime_columns_with_required_expiry_and_nullable_lifecycle_dates(): void
    {
        $migration = file_get_contents(database_path('migrations/2026_07_27_000000_create_license_activation_requests_table.php'));

        $this->assertStringContainsString("->dateTime('requested_at')", $migration);
        $this->assertStringContainsString("->dateTime('expires_at')->index()", $migration);
        foreach (['last_checked_at', 'approved_at', 'completed_at'] as $column) {
            $this->assertStringContainsString("->dateTime('{$column}')->nullable()", $migration);
        }
        $this->assertStringNotContainsString("->timestamp('expires_at')", $migration);
        $this->assertFalse(Schema::getColumns('license_activation_requests')[12]['nullable']);
        $this->assertFalse(Schema::getColumns('license_activation_requests')[13]['nullable']);
        $this->assertNull(LicenseActivationRequest::query()->create($this->requestAttributes())?->last_checked_at);
    }

    public function test_request_dates_cast_and_expiry_and_current_lookup_remain_datetime_safe(): void
    {
        $installation = app(LicenseManager::class)->installationId();
        $older = LicenseActivationRequest::query()->create($this->requestAttributes([
            'installation_uuid' => $installation, 'remote_request_id' => 'older', 'expires_at' => now()->subMinute(),
        ]));
        $current = LicenseActivationRequest::query()->create($this->requestAttributes([
            'installation_uuid' => $installation, 'remote_request_id' => 'current',
        ]));

        $this->assertInstanceOf(DateTimeInterface::class, $current->requested_at);
        $this->assertTrue($older->expires_at->isPast());
        $this->assertTrue($current->expires_at->isFuture());
        $this->assertSame($current->id, app(NaxasActivationService::class)->current()?->id);
        $this->assertNull(DB::table('license_activation_requests')->where('id', $current->id)->value('approved_at'));
    }

    public function test_approved_token_requires_local_signature_and_preserves_existing_valid_activation(): void
    {
        [$public, $private] = $this->keyPair();
        config(['licensing.offline.public_key' => $this->encode($public)]);
        $installation = app(LicenseManager::class)->installationId();
        $valid = $this->license($private, $installation);
        app(LicenseManager::class)->activate('offline', $valid, 'example.com');
        $originalHash = app(LicenseManager::class)->current()->credential_hash;
        LicenseActivationRequest::create(['installation_uuid' => $installation, 'provider' => 'naxas_portal', 'remote_request_id' => 'r1',
            'request_token_hash' => hash_hmac('sha256', 'BRQ-TEST', hash('sha256', config('app.key'), true)),
            'request_token_ciphertext' => 'BRQ-TEST', 'masked_request_token' => 'BRQ-****', 'normalized_domain' => 'example.com',
            'product_reference' => 'buildora-cms', 'application_version' => '1.0.0', 'portal_url' => 'https://licenses.naxasltd.com/activate',
            'status' => 'pending', 'requested_at' => now(), 'expires_at' => now()->addDay()]);
        Http::fake(['*' => Http::response(['status' => 'approved', 'signed_license' => $valid.'tampered'])]);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password', 'is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.license.check-activation'))->assertSessionHas('error');
        $this->assertSame($originalHash, app(LicenseManager::class)->current()->credential_hash);
        $this->assertSame('pending', LicenseActivationRequest::first()->status);
    }

    public function test_portal_mutations_are_blocked_in_runtime_demo_mode(): void
    {
        config(['cms.runtime_demo_mode' => true]);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password', 'is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.license.request-activation'))->assertForbidden();
        Http::assertNothingSent();
    }

    private function license(string $private, string $installation): string
    {
        $payload = json_encode(['license_id' => 'l1', 'product' => 'buildora-cms', 'license_type' => 'single_site',
            'installation_uuid' => $installation, 'hosts' => ['example.com'], 'expires_at' => now()->addDay()->toAtomString()], JSON_THROW_ON_ERROR);
        openssl_sign($payload, $signature, $private, OPENSSL_ALGO_SHA256);

        return $this->encode($payload).'.'.$this->encode($signature);
    }

    private function requestAttributes(array $overrides = []): array
    {
        return array_merge([
            'installation_uuid' => 'df594840-2d8b-4f41-95b6-e75dc405312f', 'provider' => 'naxas_portal',
            'remote_request_id' => 'request-'.uniqid(), 'request_token_hash' => str_repeat('a', 64),
            'request_token_ciphertext' => 'BRQ-TEST', 'masked_request_token' => 'BRQ-****',
            'normalized_domain' => 'example.com', 'product_reference' => 'buildora-cms',
            'application_version' => '1.0.0', 'portal_url' => 'https://licenses.naxasltd.com/activate',
            'status' => 'pending', 'requested_at' => now(), 'expires_at' => now()->addDay(),
            'last_checked_at' => null, 'approved_at' => null, 'completed_at' => null,
        ], $overrides);
    }

    private function encode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function keyPair(): array
    {
        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($key, $private);

        return [openssl_pkey_get_details($key)['key'], $private];
    }
}
