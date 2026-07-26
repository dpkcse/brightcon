<?php

namespace Tests\Feature;

use App\Enums\InstallationState;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Installation\DatabaseConnectionTester;
use App\Services\Installation\EnvironmentWriter;
use App\Services\Installation\InstallationManager;
use App\Services\Installation\InstallationStateService;
use App\Services\Installation\PermissionChecker;
use App\Services\Installation\RequirementChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
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

    public function test_installer_form_and_review_accept_an_empty_database_password_without_persisting_it(): void
    {
        $this->get(route('install.database'))
            ->assertOk()
            ->assertSee('name="db_password" autocomplete="new-password"', false)
            ->assertDontSee('name="db_password" autocomplete="new-password" required', false);

        $payload = $this->installerPayload('');
        $this->post(route('install.review'), $payload)
            ->assertOk()
            ->assertDontSee('name="db_password" required', false)
            ->assertDontSee($payload['admin_password']);

        $this->assertArrayNotHasKey('db_password', session('installer.safe'));
        $this->assertSame('', session('installer.safe.db_password', ''));
    }

    public function test_installer_final_execution_receives_empty_database_password_as_a_string(): void
    {
        Log::spy();
        $manager = Mockery::mock(InstallationManager::class);
        $manager->shouldReceive('install')->once()->withArgs(function (array $input): bool {
            return $input['db_password'] === '' && $input['admin_password'] === 'LongAdminSecret!1';
        })->andReturn(['admin_email' => 'admin@example.test']);
        $this->app->instance(InstallationManager::class, $manager);

        $this->post(route('install.process'), [...$this->installerPayload(''), 'confirm' => '1'])
            ->assertRedirect(route('install.complete'))
            ->assertSessionMissing('installer.safe');

        $this->assertArrayNotHasKey('db_password', session('installer.complete'));
        Log::shouldNotHaveReceived('error');
    }

    public function test_installer_keeps_administrator_password_validation_strong(): void
    {
        $payload = $this->installerPayload('');
        $payload['admin_password'] = '';
        $payload['admin_password_confirmation'] = '';

        $this->post(route('install.review'), $payload)->assertSessionHasErrors('admin_password');

        $payload['admin_password'] = 'weak';
        $payload['admin_password_confirmation'] = 'weak';
        $this->post(route('install.review'), $payload)->assertSessionHasErrors('admin_password');
    }

    public function test_installer_environment_writer_emits_an_unquoted_empty_value(): void
    {
        $originalBasePath = app()->basePath();
        $temporaryBasePath = sys_get_temp_dir().'/buildora-env-'.uniqid();
        mkdir($temporaryBasePath);
        file_put_contents($temporaryBasePath.'/.env.example', "APP_NAME=Example\nDB_PASSWORD=placeholder\n");
        app()->setBasePath($temporaryBasePath);

        try {
            app(EnvironmentWriter::class)->write(['DB_PASSWORD' => '']);
            $this->assertStringContainsString("DB_PASSWORD=\n", file_get_contents($temporaryBasePath.'/.env'));
            $this->assertStringNotContainsString('DB_PASSWORD=""', file_get_contents($temporaryBasePath.'/.env'));
        } finally {
            app()->setBasePath($originalBasePath);
            @unlink($temporaryBasePath.'/.env');
            @unlink($temporaryBasePath.'/.env.example');
            @rmdir($temporaryBasePath);
        }
    }

    public function test_installer_database_tester_passes_empty_and_non_empty_passwords_unchanged(): void
    {
        foreach (['', 'valid password with spaces'] as $password) {
            DB::shouldReceive('connection')->once()->with('installer_probe')->andReturn(new class($this, $password)
            {
                public function __construct(private InstallationFoundationTest $test, private string $expected) {}

                public function getPdo(): object
                {
                    $this->test->assertSame($this->expected, config('database.connections.installer_probe.password'));

                    return new \stdClass;
                }
            });
            DB::shouldReceive('purge')->once()->with('installer_probe');

            $result = app(DatabaseConnectionTester::class)->test([
                'host' => 'localhost', 'port' => 3306, 'database' => 'buildora', 'username' => 'root', 'password' => $password,
            ]);

            $this->assertTrue($result['passed']);
        }
    }

    public function test_installer_cli_accepts_an_explicitly_empty_database_password(): void
    {
        $manager = Mockery::mock(InstallationManager::class);
        $manager->shouldReceive('install')->once()->withArgs(fn (array $input): bool => $input['db_password'] === '')->andReturn(['admin_email' => 'admin@example.test']);
        $this->app->instance(InstallationManager::class, $manager);

        $this->artisan('cms:install', [
            '--app-name' => 'Buildora CMS', '--app-url' => 'https://example.test', '--db-host' => 'localhost', '--db-port' => '3306', '--db-name' => 'buildora', '--db-user' => 'root', '--db-password' => '', '--admin-name' => 'Administrator', '--admin-email' => 'admin@example.test', '--admin-password' => 'LongAdminSecret!1', '--force-non-interactive-confirmation' => true,
        ])->expectsConfirmation('Install without deleting or resetting existing data?', 'yes')->assertSuccessful();
    }

    private function installerPayload(string $databasePassword): array
    {
        return ['app_name' => 'Buildora CMS', 'app_url' => 'https://example.test', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'buildora', 'db_user' => 'cms', 'db_password' => $databasePassword, 'admin_name' => 'Administrator', 'admin_email' => 'admin@example.test', 'admin_password' => 'LongAdminSecret!1', 'admin_password_confirmation' => 'LongAdminSecret!1', 'seed' => 'clean', 'storage_link' => 1];
    }
}
