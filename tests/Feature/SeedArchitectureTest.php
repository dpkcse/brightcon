<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\GalleryImage;
use App\Models\HomepageSection;
use App\Models\Organization;
use App\Models\PartnerMessage;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Slider;
use App\Models\SocialLink;
use App\Models\ThemeSetting;
use App\Models\User;
use App\Services\Settings\SettingsService;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Demo\DemoContentSeeder;
use Database\Seeders\EssentialSystemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class SeedArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        foreach (['CMS_SEED_MODE', 'CMS_ADMIN_NAME', 'CMS_ADMIN_EMAIL', 'CMS_ADMIN_PASSWORD', 'CMS_ALLOW_DEMO_SEED_IN_PRODUCTION', 'CMS_ALLOW_DEMO_SEED_WITH_EXISTING_DATA'] as $key) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);
        }
        app()->detectEnvironment(fn (): string => 'testing');
        parent::tearDown();
    }

    public function test_default_and_explicit_clean_modes_seed_only_essential_records_idempotently(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->setEnv('CMS_SEED_MODE', 'clean');
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, SiteSetting::count());
        $this->assertSame(1, ThemeSetting::count());
        $this->assertEqualsCanonicalizing(['about', 'gallery_cta', 'partner_messages', 'project_highlights', 'services'], HomepageSection::pluck('section_key')->all());
        $this->assertSame(0, User::count());
        $this->assertSame(0, Service::count());
        $this->assertSame(0, Project::count());
        $this->assertSame(0, Organization::count());
        $this->assertSame(0, GalleryImage::count());
        $this->assertSame(0, PartnerMessage::count());
        $this->assertSame(0, SocialLink::count());
        $this->assertSame(0, ContactMessage::count());
        $this->assertNull(SiteSetting::first()->email);
        $this->assertNull(SiteSetting::first()->installation_completed_at);

        foreach (['/', '/admin/login', '/services', '/projects', '/gallery', '/contact'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_invalid_mode_fails_clearly(): void
    {
        $this->setEnv('CMS_SEED_MODE', 'surprise');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected clean or demo');
        $this->seed(DatabaseSeeder::class);
    }

    public function test_demo_mode_adds_fictional_text_only_content_and_is_idempotent(): void
    {
        $this->setEnv('CMS_SEED_MODE', 'demo');
        $this->seed(DatabaseSeeder::class);
        Service::where('slug', 'buildora-demo-building-and-structural-construction')->update(['title' => 'Administrator edited title']);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(6, Service::count());
        $this->assertSame(6, ProjectCategory::count());
        $this->assertSame(6, Project::count());
        $this->assertSame(6, Organization::count());
        $this->assertSame(3, Slider::count());
        $this->assertSame(1, PartnerMessage::count());
        $this->assertSame('Administrator edited title', Service::where('slug', 'buildora-demo-building-and-structural-construction')->value('title'));
        $this->assertSame('Buildora Construction & Engineering', SiteSetting::first()->company_name);
        $this->assertFalse((bool) SiteSetting::first()->show_contact_map);
        $this->assertSame(0, GalleryImage::count());
        $this->assertSame(0, SocialLink::count());
        $this->assertSame(0, Service::whereNotNull('image')->count());
        $this->assertSame(0, Project::whereNotNull('featured_image')->count());
        $this->assertSame(0, Slider::whereNotNull('image')->count());
        $this->assertTrue(Project::with('category')->get()->every(fn (Project $project): bool => $project->category !== null && $project->status));

        $this->seed(EssentialSystemSeeder::class);
        $this->assertSame(6, Service::count());
    }

    public function test_existing_settings_and_customer_content_are_not_overwritten(): void
    {
        SiteSetting::create(['company_name' => 'Customer Company', 'email' => 'owner@example.net']);
        ThemeSetting::create(['primary_color' => '#123456']);
        Service::create(['title' => 'Customer Service', 'slug' => 'customer-service']);

        $this->seed(EssentialSystemSeeder::class);
        $this->assertSame('Customer Company', SiteSetting::first()->company_name);
        $this->assertSame('#123456', ThemeSetting::first()->primary_color);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Existing customer content detected');
        $this->seed(DemoContentSeeder::class);
    }

    public function test_demo_override_adds_records_without_changing_customer_records(): void
    {
        Service::create(['title' => 'Customer Service', 'slug' => 'customer-service', 'short_description' => 'Keep me']);
        Service::create(['title' => 'Industrial Construction', 'slug' => 'customer-industrial-construction', 'short_description' => 'Same title, buyer owned']);
        $this->setEnv('CMS_ALLOW_DEMO_SEED_WITH_EXISTING_DATA', 'true');
        $this->seed(DemoContentSeeder::class);

        $this->assertDatabaseHas('services', ['slug' => 'customer-service', 'short_description' => 'Keep me']);
        $this->assertDatabaseHas('services', ['slug' => 'customer-industrial-construction', 'short_description' => 'Same title, buyer owned']);
        $this->assertCount(8, Service::all());
    }

    public function test_production_demo_requires_a_separate_explicit_acknowledgement(): void
    {
        app()->detectEnvironment(fn (): string => 'production');
        try {
            app(DemoContentSeeder::class)->run();
            $this->fail('Production demo seeding should have failed.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('CMS_ALLOW_DEMO_SEED_IN_PRODUCTION', $exception->getMessage());
        }

        $this->setEnv('CMS_ALLOW_DEMO_SEED_IN_PRODUCTION', 'true');
        app(DemoContentSeeder::class)->run();
        $this->assertSame(6, Service::count());
    }

    public function test_seeded_settings_replace_a_warmed_empty_fallback(): void
    {
        $settings = app(SettingsService::class);
        $this->assertSame('Your Company', $settings->string('company_name'));
        $this->seed(EssentialSystemSeeder::class);
        $this->assertSame('Your Company', $settings->string('company_name'));
        $this->assertSame(1, SiteSetting::count());
    }

    public function test_explicit_admin_environment_credentials_create_once_without_resetting_password(): void
    {
        $this->setEnv('CMS_ADMIN_NAME', 'Secure Operator');
        $this->setEnv('CMS_ADMIN_EMAIL', 'operator@example.com');
        $this->setEnv('CMS_ADMIN_PASSWORD', 'Strong!Pass123');
        $this->seed(EssentialSystemSeeder::class);
        $hash = User::first()->password;
        $this->setEnv('CMS_ADMIN_PASSWORD', 'Different!Pass123');
        $this->seed(EssentialSystemSeeder::class);

        $this->assertSame(1, User::count());
        $this->assertTrue(User::first()->is_admin);
        $this->assertTrue(Hash::check('Strong!Pass123', $hash));
        $this->assertSame($hash, User::first()->password);
    }

    private function setEnv(string $key, string $value): void
    {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
