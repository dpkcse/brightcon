<?php

namespace Tests\Feature;

use App\Enums\InstallationState;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Installation\EnvironmentWriter;
use App\Services\Installation\InstallationStateService;
use App\Services\Installation\PermissionChecker;
use App\Services\Installation\RequirementChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallationFoundationTest extends TestCase
{
    use RefreshDatabase;

    private string $marker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->marker = storage_path('app/.installed');
        @unlink($this->marker);
    }

    protected function tearDown(): void
    {
        @unlink($this->marker);
        parent::tearDown();
    }

    public function test_partial_database_is_not_misclassified_as_installed(): void
    {
        $state = app(InstallationStateService::class);
        $this->assertSame(InstallationState::PartiallyInstalled, $state->state());
        $this->assertTrue($state->canRunInstaller());
    }

    public function test_marker_and_database_completion_are_installed_and_lock_installer(): void
    {
        User::query()->create(['name' => 'Admin', 'email' => uniqid().'@example.test', 'password' => 'irrelevant', 'is_admin' => true]);
        SiteSetting::query()->create(['company_name' => 'Safe Site', 'installation_completed_at' => now(), 'installed_version' => '1.0.0']);
        app(InstallationStateService::class)->markInstalled();
        $this->assertTrue(app(InstallationStateService::class)->isInstalled());
        config(['installer.enforce' => true]);
        $this->get('/install?debug=1')->assertRedirect(route('admin.login'));
    }

    public function test_database_completion_without_marker_is_inconsistent(): void
    {
        User::query()->create(['name' => 'Admin', 'email' => uniqid().'@example.test', 'password' => 'irrelevant', 'is_admin' => true]);
        SiteSetting::query()->create(['installation_completed_at' => now(), 'installed_version' => '1.0.0']);
        $this->assertSame(InstallationState::Inconsistent, app(InstallationStateService::class)->state());
    }

    public function test_legacy_installation_remains_operational(): void
    {
        User::query()->create(['name' => 'Admin', 'email' => uniqid().'@example.test', 'password' => 'irrelevant', 'is_admin' => true]);
        SiteSetting::query()->create(['company_name' => 'Legacy']);
        $this->assertSame(InstallationState::LegacyInstalled, app(InstallationStateService::class)->state());
        config(['installer.enforce' => true]);
        $this->get('/')->assertOk();
    }

    public function test_requirement_and_permission_reports_are_non_secret(): void
    {
        $requirements = app(RequirementChecker::class)->check();
        $this->assertNotEmpty($requirements);
        $this->assertArrayHasKey('blocking', $requirements[0]);
        $this->assertTrue(collect(app(PermissionChecker::class)->check())->every('passed'));
        $this->artisan('cms:requirements')->assertExitCode(app(RequirementChecker::class)->passes() ? 0 : 1);
    }

    public function test_environment_writer_rejects_newline_injection_before_writing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        app(EnvironmentWriter::class)->write(['APP_NAME' => "unsafe\nDB_PASSWORD=leak"]);
    }

    public function test_review_response_and_session_exclude_secrets(): void
    {
        $payload = ['app_name' => 'Buildora CMS', 'app_url' => 'https://example.test', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'buildora', 'db_user' => 'cms', 'db_password' => 'DatabaseSecret!1', 'admin_name' => 'Administrator', 'admin_email' => 'admin@example.test', 'admin_password' => 'LongAdminSecret!1', 'admin_password_confirmation' => 'LongAdminSecret!1', 'seed' => 'clean', 'storage_link' => 1];
        $response = $this->post(route('install.review'), $payload)->assertOk()->assertDontSee('DatabaseSecret!1')->assertDontSee('LongAdminSecret!1');
        $this->assertArrayNotHasKey('db_password', session('installer.safe'));
        $this->assertArrayNotHasKey('admin_password', session('installer.safe'));
    }
}
