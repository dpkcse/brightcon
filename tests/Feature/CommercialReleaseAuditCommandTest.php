<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CommercialReleaseAuditCommandTest extends TestCase
{
    private string $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = sys_get_temp_dir().'/commercial-audit-'.bin2hex(random_bytes(8));
        File::makeDirectory($this->fixture, 0755, true);
        config()->set('commercial_release.required_files', ['.env.example', 'THIRD-PARTY-LICENSES.md']);
        config()->set('commercial_release.unverified_assets', []);
        config()->set('commercial_release.forbidden_branding', [
            ['term' => 'BrightCon', 'severity' => 'warning'],
        ]);
        File::put($this->fixture.'/.env.example', "APP_NAME=Example\n");
        File::put($this->fixture.'/THIRD-PARTY-LICENSES.md', "# Notices\n");
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->fixture);
        parent::tearDown();
    }

    public function test_clean_fixture_passes_without_modification(): void
    {
        $before = $this->snapshot();
        $exit = $this->audit();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('PASS No blocking failures found', Artisan::output());
        $this->assertSame($before, $this->snapshot());
    }

    public function test_env_style_file_fails_and_secret_is_redacted(): void
    {
        File::put($this->fixture.'/.env.production', "API_KEY=do-not-display-this-value\n");

        $this->assertSame(1, $this->audit());
        $this->assertStringContainsString('FAIL [environment file]', Artisan::output());
        $this->assertStringNotContainsString('do-not-display-this-value', Artisan::output());
    }

    public function test_log_in_candidate_is_a_blocking_failure(): void
    {
        File::put($this->fixture.'/application.log', 'runtime output');

        $this->assertSame(1, $this->audit());
        $this->assertStringContainsString('sensitive/generated extension', Artisan::output());
    }

    public function test_forbidden_branding_warns_without_changing_exit_code(): void
    {
        File::put($this->fixture.'/readme.txt', 'BrightCon placeholder');

        $this->assertSame(0, $this->audit());
        $this->assertStringContainsString('WARNING [forbidden branding]', Artisan::output());
    }

    public function test_database_dump_extension_fails(): void
    {
        File::put($this->fixture.'/customer.dump', 'not a real database');

        $this->assertSame(1, $this->audit());
        $this->assertStringContainsString('customer.dump', Artisan::output());
    }

    public function test_missing_third_party_license_document_fails(): void
    {
        File::delete($this->fixture.'/THIRD-PARTY-LICENSES.md');

        $this->assertSame(1, $this->audit());
        $output = Artisan::output();
        $this->assertStringContainsString('missing required file', $output);
        $this->assertStringContainsString('THIRD-PARTY-LICENSES.md', $output);
    }

    public function test_configured_fail_severity_for_branding_returns_failure(): void
    {
        config()->set('commercial_release.forbidden_branding', [
            ['term' => 'BrightCon', 'severity' => 'fail'],
        ]);
        File::put($this->fixture.'/notice.txt', 'BrightCon placeholder');

        $this->assertSame(1, $this->audit());
        $this->assertStringContainsString('FAIL [forbidden branding]', Artisan::output());
    }

    private function audit(): int
    {
        return Artisan::call('commercial:audit', ['--path' => $this->fixture, '--no-git' => true]);
    }

    /** @return array<string, string> */
    private function snapshot(): array
    {
        $snapshot = [];
        foreach (File::allFiles($this->fixture, true) as $file) {
            $snapshot[$file->getRelativePathname()] = hash_file('sha256', $file->getPathname());
        }
        ksort($snapshot);

        return $snapshot;
    }
}
