<?php

namespace Tests\Feature;

use App\Services\Release\ReleaseBuilder;
use App\Services\Release\ReleaseScanner;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Tests\TestCase;

class ReleasePackagingTest extends TestCase
{
    public function test_variant_selection_enforces_distribution_policy(): void
    {
        $builder = app(ReleaseBuilder::class);
        $source = $builder->select('source');
        $shared = $builder->select('shared_hosting');
        $docs = $builder->select('documentation');

        $this->assertContains('.env.example', $source);
        $this->assertContains('public/build/manifest.json', $source);
        $this->assertNotContains('tests/TestCase.php', $source);
        $this->assertNotContains('.env', $source);
        $this->assertFalse((bool) array_filter($source, fn ($path) => str_starts_with($path, 'vendor/') || str_contains($path, 'uploads/')));
        $this->assertContains('public/build/manifest.json', $shared);
        $this->assertFalse((bool) array_filter($shared, fn ($path) => str_starts_with($path, 'tests/')));
        $this->assertContains('documentation/read-me-first.md', $docs);
        $this->assertNotContains('documentation/audits/phase-g-pre-change-release-audit.md', $docs);
    }

    public function test_security_scanner_redacts_and_blocks_dangerous_files(): void
    {
        $root = sys_get_temp_dir().'/release-scan-'.bin2hex(random_bytes(6));
        File::makeDirectory($root);
        File::put($root.'/.env', "APP_KEY=base64:this-must-never-print\n");
        File::put($root.'/signing.pem', "-----BEGIN PRIVATE KEY-----\nsecret\n");
        $findings = app(ReleaseScanner::class)->scan($root);
        File::deleteDirectory($root);

        $this->assertNotEmpty($findings);
        $this->assertSame(['[REDACTED]'], array_values(array_unique(array_column($findings, 'context'))));
        $this->assertStringNotContainsString('this-must-never-print', json_encode($findings));
    }

    public function test_dry_run_writes_no_archive_and_internal_acknowledgement_is_required(): void
    {
        File::deleteDirectory(base_path('release'));
        $this->assertSame(0, Artisan::call('cms:release', ['--variant' => 'source', '--dry-run' => true]));
        $this->assertFalse(File::exists(base_path('release')));
        $this->assertSame(1, Artisan::call('cms:release', ['--variant' => 'source', '--approval-state' => 'internal_test']));
        $this->assertStringContainsString('require --allow-unapproved-license', Artisan::output());
    }

    public function test_unsafe_output_and_commercial_approval_are_rejected(): void
    {
        $builder = app(ReleaseBuilder::class);
        $this->expectException(RuntimeException::class);
        $builder->build('source', '1.0.0', 'internal_test', storage_path('release'), true, false, false);
    }
}
