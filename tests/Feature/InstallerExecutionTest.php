<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Installation\InstallationManager;
use App\Services\Installation\InstallationStateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class InstallerExecutionTest extends TestCase
{
    use RefreshDatabase;

    private string $marker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->marker = storage_path('app/.installed');
        @unlink($this->marker);
        config(['installer.execution_attempts' => 5, 'installer.execution_decay_minutes' => 10]);
    }

    protected function tearDown(): void
    {
        @unlink($this->marker);
        parent::tearDown();
    }

    public function test_get_execution_never_runs_installer_and_redirects_to_safe_step(): void
    {
        $manager = Mockery::mock(InstallationManager::class);
        $manager->shouldNotReceive('install');
        $this->app->instance(InstallationManager::class, $manager);

        $this->get('/install/run')->assertRedirect(route('install.welcome'));
        $this->withSession(['installer.safe' => ['app_name' => 'Buildora CMS']])
            ->get('/install/run')->assertRedirect(route('install.review'));
    }

    public function test_validation_retry_is_allowed_and_failures_redirect_without_secrets(): void
    {
        Log::spy();
        $payload = $this->payload();
        $payload['admin_password_confirmation'] = 'different-secret';

        foreach (range(1, 2) as $attempt) {
            $response = $this->post('/install/run', $payload)
                ->assertRedirect(route('install.application'))
                ->assertSessionHasErrors('admin_password')
                ->assertDontSee($payload['db_password'])
                ->assertDontSee($payload['admin_password']);
            $this->assertStringNotContainsString($payload['db_password'], serialize(session()->all()));
            $this->assertStringNotContainsString($payload['admin_password'], serialize(session()->all()));
        }

        Log::shouldNotHaveReceived('error');
    }

    public function test_repeated_html_attempts_receive_friendly_safe_429(): void
    {
        $payload = $this->payload();
        $payload['admin_password'] = 'weak';
        $payload['admin_password_confirmation'] = 'weak';

        foreach (range(1, 5) as $attempt) {
            $this->post('/install/run', $payload)->assertRedirect();
        }

        $this->post('/install/run', $payload)
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertSee('Too many installation attempts were submitted')
            ->assertSee('Back to installer')
            ->assertDontSee($this->payload()['db_password'])
            ->assertDontSee($this->payload()['admin_password']);
        $this->assertStringNotContainsString($this->payload()['db_password'], serialize(session()->all()));
        $this->assertStringNotContainsString($this->payload()['admin_password'], serialize(session()->all()));
    }

    public function test_repeated_json_attempts_receive_safe_429_with_retry_after(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->postJson('/install/run', [])->assertStatus(422);
        }

        $this->postJson('/install/run', $this->payload())
            ->assertStatus(429)
            ->assertJsonStructure(['message', 'retry_after'])
            ->assertJson(['message' => 'Too many installation attempts. Please wait and try again.'])
            ->assertJsonMissing(['db_password' => $this->payload()['db_password']])
            ->assertJsonMissing(['admin_password' => $this->payload()['admin_password']]);
    }

    public function test_success_and_runtime_failure_follow_post_redirect_get(): void
    {
        $manager = Mockery::mock(InstallationManager::class);
        $manager->shouldReceive('install')->once()->andReturn([
            'version' => '1.0.0', 'admin_email' => 'admin@example.test', 'app_name' => 'Buildora CMS',
            'seed' => 'clean', 'storage_status' => 'present',
        ]);
        $this->app->instance(InstallationManager::class, $manager);

        $this->post('/install/run', $this->payload())->assertRedirect(route('install.complete'));
        $this->get('/install/complete')->assertOk();
        $this->get('/install/complete')->assertOk();

        $failing = Mockery::mock(InstallationManager::class);
        $failing->shouldReceive('install')->once()->andThrow(new RuntimeException('Database connection failed safely.'));
        $this->app->instance(InstallationManager::class, $failing);
        $this->withSession(['installer.safe' => ['app_name' => 'Buildora CMS']])
            ->post('/install/run', $this->payload())
            ->assertRedirect(route('install.review'))
            ->assertSessionHasErrors('installation');
    }

    public function test_filesystem_lock_prevents_a_second_pipeline_execution(): void
    {
        $handle = fopen(storage_path('app/.installation.lock'), 'c+');
        $this->assertIsResource($handle);
        $this->assertTrue(flock($handle, LOCK_EX | LOCK_NB));

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Another installation is already running.');
            app(InstallationManager::class)->install($this->payload());
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    public function test_completed_installation_blocks_execution_but_allows_completion_refresh(): void
    {
        User::query()->create(['name' => 'Admin', 'email' => 'installed@example.test', 'password' => 'irrelevant', 'is_admin' => true]);
        SiteSetting::query()->create(['company_name' => 'Buildora CMS', 'installation_completed_at' => now(), 'installed_version' => '1.0.0']);
        app(InstallationStateService::class)->markInstalled();

        $manager = Mockery::mock(InstallationManager::class);
        $manager->shouldNotReceive('install');
        $this->app->instance(InstallationManager::class, $manager);
        $result = ['version' => '1.0.0', 'admin_email' => 'installed@example.test', 'app_name' => 'Buildora CMS', 'seed' => 'clean', 'storage_status' => 'present'];

        $this->post('/install/run', $this->payload())->assertRedirect(route('admin.login'));
        $this->withSession(['installer.complete' => $result])->get('/install/run')->assertRedirect(route('install.complete'));
        $this->get('/install/complete')->assertOk();
        $this->get('/install/complete')->assertOk();
    }

    private function payload(): array
    {
        return ['app_name' => 'Buildora CMS', 'app_url' => 'https://example.test', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'buildora', 'db_user' => 'cms', 'db_password' => 'DatabaseSecret!1', 'admin_name' => 'Administrator', 'admin_email' => 'admin@example.test', 'admin_password' => 'LongAdminSecret!1', 'admin_password_confirmation' => 'LongAdminSecret!1', 'seed' => 'clean', 'storage_link' => 1, 'confirm' => '1'];
    }
}
