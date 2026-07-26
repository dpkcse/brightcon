<?php

namespace Tests\Feature;

use App\Contracts\SettingsRepositoryInterface;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PhaseCProductIdentityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_product_and_company_fallbacks_are_separate(): void
    {
        $settings = app(SettingsRepositoryInterface::class);
        $this->assertSame('Buildora CMS', $settings->string('product_name'));
        $this->assertSame('1.0.0', $settings->string('product_version'));
        $this->assertSame('Your Company', $settings->string('company_name'));
        $this->assertNotSame($settings->string('product_name'), $settings->string('company_name'));

        SiteSetting::create(['company_name' => 'ABC Construction Ltd.', 'product_name' => null, 'product_version' => null]);
        $settings->refresh();
        $this->assertSame('ABC Construction Ltd.', $settings->string('company_name'));
        $this->assertSame('Buildora CMS', $settings->string('product_name'));
        $this->assertSame(config('cms.product.version'), $settings->string('product_version'));
    }

    public function test_admin_branding_is_dynamic_and_powered_by_is_optional(): void
    {
        SiteSetting::create(['company_name' => 'ABC Construction Ltd.', 'show_powered_by' => false]);
        app(SettingsRepositoryInterface::class)->refresh();
        $this->get(route('admin.login'))->assertOk()->assertSee('ABC Construction Ltd.')->assertDontSee('Powered by Buildora CMS')->assertDontSee('Bright'.'Con');

        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password', 'is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('ABC Construction Ltd.')->assertSee('Buildora CMS')->assertSee('Version 1.0.0')->assertDontSee('Bright'.'Con');
    }

    public function test_public_identity_seo_footer_and_uploaded_asset_precedence(): void
    {
        SiteSetting::create([
            'company_name' => 'ABC Construction Ltd.', 'tagline' => 'A customer tagline',
            'logo' => 'uploads/site/logo/customer.png', 'favicon' => 'uploads/site/favicon/customer.png',
            'show_powered_by' => true,
        ]);
        app(SettingsRepositoryInterface::class)->refresh();
        $response = $this->get(route('home'))->assertOk()->assertSee('ABC Construction Ltd.')
            ->assertSee('og:site_name', false)->assertSee('Powered by Buildora CMS')
            ->assertSee('/storage/uploads/site/logo/customer.png', false)
            ->assertSee('/storage/uploads/site/favicon/customer.png', false)
            ->assertDontSee('Bright'.'Con');
        $this->assertStringContainsString('ABC Construction Ltd.', $response->getContent());
    }

    public function test_maintenance_error_and_existing_public_routes_have_generic_identity(): void
    {
        SiteSetting::create(['company_name' => 'ABC Construction Ltd.']);
        app(SettingsRepositoryInterface::class)->refresh();
        foreach (['about', 'competency', 'equipment.index', 'gallery.index', 'projects.index', 'services.index', 'contact.index'] as $route) {
            $this->get(route($route))->assertOk()->assertDontSee('Bright'.'Con');
        }
        $this->get('/not-a-real-page')->assertNotFound()->assertDontSee('Bright'.'Con');

        SiteSetting::first()->update(['website_status' => 'maintenance']);
        app(SettingsRepositoryInterface::class)->refresh();
        $this->get(route('home'))->assertStatus(503)->assertSee('ABC Construction Ltd.')->assertDontSee('Bright'.'Con');
    }

    public function test_metadata_and_environment_defaults_are_generic(): void
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('buildora/construction-cms', $composer['name']);
        $this->assertStringContainsString('APP_NAME="Buildora CMS"', file_get_contents(base_path('.env.example')));
        $this->assertStringContainsString('DB_DATABASE=buildora_cms', file_get_contents(base_path('.env.example')));
        $this->assertStringNotContainsString('Bright'.'Con', file_get_contents(config_path('cms.php')));
    }

    public function test_existing_company_assets_and_map_values_are_not_rewritten_by_migrations(): void
    {
        $setting = SiteSetting::create([
            'company_name' => 'Existing Company', 'logo' => 'uploads/existing-logo.png',
            'favicon' => 'uploads/existing-icon.png', 'google_map_embed_url' => 'https://www.google.com/maps/embed?pb=existing',
        ]);

        $setting->refresh();
        $this->assertSame('Existing Company', $setting->company_name);
        $this->assertSame('uploads/existing-logo.png', $setting->logo);
        $this->assertSame('uploads/existing-icon.png', $setting->favicon);
        $this->assertSame('https://www.google.com/maps/embed?pb=existing', $setting->google_map_embed_url);
    }
}
