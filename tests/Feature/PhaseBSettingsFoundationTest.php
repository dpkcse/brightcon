<?php

namespace Tests\Feature;

use App\Contracts\SettingsRepositoryInterface;
use App\Models\SiteSetting;
use App\Models\ThemeSetting;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhaseBSettingsFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (array_merge(config('cms.cache.legacy'), [config('cms.cache.site'), config('cms.cache.theme'), config('cms.cache.frontend')]) as $key) {
            Cache::forget($key);
        }
    }

    public function test_settings_service_is_typed_cached_and_safe_when_rows_are_missing(): void
    {
        SiteSetting::create(['company_name' => 'Existing Co', 'show_contact_map' => true, 'map_zoom' => 17]);
        ThemeSetting::create(['primary_color' => '#abcdef']);
        $queries = 0;
        DB::listen(function () use (&$queries) {
            $queries++;
        });
        $settings = app(SettingsRepositoryInterface::class);
        $this->assertSame('Existing Co', $settings->string('company_name'));
        $this->assertTrue($settings->bool('show_contact_map'));
        $this->assertSame(17, $settings->int('map_zoom'));
        $this->assertSame('#ABCDEF', $settings->color('primary_color'));
        $afterFirst = $queries;
        $settings->site();
        $settings->theme();
        $settings->string('company_name');
        $this->assertSame($afterFirst, $queries);

        SiteSetting::query()->delete();
        ThemeSetting::query()->delete();
        $settings->refresh();
        $this->assertSame('Your Company', $settings->string('company_name'));
        $this->assertSame(15, $settings->int('map_zoom'));
    }

    public function test_settings_service_handles_not_yet_migrated_tables(): void
    {
        Schema::drop('site_settings');
        Schema::drop('theme_settings');
        $settings = app(SettingsRepositoryInterface::class);
        $settings->refresh();
        $this->assertSame('Your Company', $settings->site()->company_name);
        $this->assertSame('#d80d4c', $settings->theme()->primary_color);
    }

    public function test_guest_non_admin_and_admin_settings_authorization(): void
    {
        $this->get(route('admin.settings.general.edit'))->assertRedirect(route('admin.login'));
        $user = User::create(['name' => 'User', 'email' => 'user@example.test', 'password' => 'password']);
        $this->actingAs($user)->get(route('admin.settings.general.edit'))->assertForbidden();
        $this->actingAs($user)->put(route('admin.settings.general.update'), [])->assertForbidden();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password', 'is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.settings.general.edit'))->assertOk();
    }

    public function test_theme_validation_blocks_css_and_dimensions_but_allows_safe_css(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password', 'is_admin' => true]);
        foreach (['@import "evil";', 'a{background:url(x)}', 'a{width:expression(x)}', '</style><script>x</script>'] as $css) {
            $this->actingAs($admin)->put(route('admin.settings.theme.update'), ['custom_css_enabled' => 1, 'custom_css' => $css])->assertSessionHasErrors('custom_css');
        }
        $this->actingAs($admin)->put(route('admin.settings.theme.update'), ['primary_color' => '#abcdef', 'base_font_size' => '1rem', 'custom_css_enabled' => 1, 'custom_css' => '.notice { color: #123456; }'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('theme_settings', ['primary_color' => '#ABCDEF', 'custom_css_enabled' => true]);
        $this->get(route('home'))->assertSee('.notice { color: #123456; }', false);
        ThemeSetting::first()->update(['custom_css_enabled' => false]);
        app(SettingsRepositoryInterface::class)->forgetThemeCache();
        $this->get(route('home'))->assertDontSee('.notice { color: #123456; }', false);
    }

    public function test_validation_rejects_unsafe_configuration(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password', 'is_admin' => true]);
        $payload = ['timezone' => 'Not/AZone', 'date_format' => '<script>', 'canonical_base_url' => 'javascript:alert(1)', 'google_analytics_id' => '<script>', 'website_status' => 'broken', 'show_contact_map' => 0];
        $this->actingAs($admin)->put(route('admin.settings.general.update'), $payload)->assertSessionHasErrors(['timezone', 'date_format', 'canonical_base_url', 'google_analytics_id', 'website_status']);
    }

    public function test_setting_update_invalidates_cache_and_is_immediately_public(): void
    {
        SiteSetting::create(['company_name' => 'Before']);
        app(SettingsRepositoryInterface::class)->site();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password', 'is_admin' => true]);
        $this->actingAs($admin)->put(route('admin.settings.general.update'), ['company_name' => 'After', 'website_status' => 'active', 'show_contact_map' => 0])->assertSessionHasNoErrors();
        $this->get(route('home'))->assertSee('After');
    }

    public function test_login_is_throttled_and_success_clears_the_limiter(): void
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => Hash::make('correct'), 'is_admin' => true]);
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.submit'), ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        }
        $this->post(route('admin.login.submit'), ['email' => $user->email, 'password' => 'correct'])->assertSessionHasErrors('email');
        RateLimiter::clear(strtolower($user->email).'|127.0.0.1');
        $response = $this->post(route('admin.login.submit'), ['email' => $user->email, 'password' => 'correct']);
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame(0, RateLimiter::attempts(strtolower($user->email).'|127.0.0.1'));
    }

    public function test_maintenance_is_public_only_and_escaped(): void
    {
        SiteSetting::create(['website_status' => 'maintenance', 'maintenance_message' => '<script>alert(1)</script>']);
        Cache::forget('site_settings');
        $this->get(route('home'))->assertStatus(503)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get(route('admin.login'))->assertOk();
    }

    public function test_upload_replacement_deletes_only_owned_old_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('uploads/site/logos/dark/old.png', 'old');
        Storage::disk('public')->put('unrelated/keep.png', 'keep');
        $service = new FileUploadService;
        $new = $service->replace(UploadedFile::fake()->image('logo.png'), 'uploads/site/logos/dark/old.png', 'uploads/site/logos/dark');
        Storage::disk('public')->assertExists($new);
        Storage::disk('public')->assertMissing('uploads/site/logos/dark/old.png');
        $service->replace(UploadedFile::fake()->image('other.png'), 'unrelated/keep.png', 'uploads/site/logos/dark');
        Storage::disk('public')->assertExists('unrelated/keep.png');
    }

    public function test_system_information_is_admin_only_and_contains_no_secrets(): void
    {
        $user = User::create(['name' => 'User', 'email' => 'user@example.test', 'password' => 'password']);
        $this->actingAs($user)->get(route('admin.system-information'))->assertForbidden();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password', 'is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.system-information'))->assertOk()->assertSee('Laravel version')->assertDontSee('DB_PASSWORD')->assertDontSee('APP_KEY');
    }
}
