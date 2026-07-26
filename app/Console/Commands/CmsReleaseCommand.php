<?php

namespace App\Console\Commands;

use App\Services\Release\ReleaseBuilder;
use Illuminate\Console\Command;
use RuntimeException;

class CmsReleaseCommand extends Command
{
    protected $signature = 'cms:release
        {--variant=source : source, shared-hosting, or documentation}
        {--all : Build all variants}
        {--output= : Output directory (default: ignored release directory)}
        {--release-version= : Semantic package version (Artisan reserves --version)}
        {--approval-state=release_candidate : internal_test, release_candidate, or commercially_approved}
        {--allow-unapproved-license : Required acknowledgement for internal test builds}
        {--dry-run : List prospective files without writing output}
        {--no-archive : Retain staging tree without creating ZIP}
        {--keep-staging : Keep staging after archiving}';

    protected $description = 'Build deterministic, audited Buildora CMS distribution packages';

    public function handle(ReleaseBuilder $builder): int
    {
        $variant = str_replace('-', '_', (string) $this->option('variant'));
        $variants = $this->option('all') ? array_keys(config('commercial_release.variants')) : [$variant];
        $state = (string) $this->option('approval-state');
        $dryRun = (bool) $this->option('dry-run');
        if ($state === 'internal_test' && ! $dryRun && ! $this->option('allow-unapproved-license')) {
            $this->error('Internal test builds require --allow-unapproved-license acknowledgement.');

            return self::FAILURE;
        }
        if ($this->option('allow-unapproved-license') && $state !== 'internal_test') {
            $this->error('--allow-unapproved-license is valid only with --approval-state=internal_test.');

            return self::FAILURE;
        }

        $output = (string) ($this->option('output') ?: config('commercial_release.output_directory'));
        $version = (string) ($this->option('release-version') ?: config('commercial_release.default_version'));
        try {
            foreach ($variants as $item) {
                $result = $builder->build($item, $version, $state, $output, $dryRun, ! $this->option('no-archive'), (bool) $this->option('keep-staging'));
                if ($dryRun) {
                    $this->info(str_replace('_', '-', $item).": {$result['count']} prospective files");
                    foreach ($result['files'] as $file) {
                        $this->line($file);
                    }
                } elseif (isset($result['archive'])) {
                    $this->info("Built {$result['archive']} ({$result['count']} inventoried files, {$result['size']} bytes)");
                    $this->line("SHA-256: {$result['checksum']}");
                } else {
                    $this->info("Staged {$result['staging']} ({$result['count']} inventoried files, {$result['size']} bytes)");
                }
            }
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
