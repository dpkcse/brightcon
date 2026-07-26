<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Process\Process;

class CommercialReleaseAudit extends Command
{
    protected $signature = 'commercial:audit {--path= : Release candidate root (defaults to the application base path)} {--no-git : Skip Git-index checks}';

    protected $description = 'Inspect a commercial release candidate without changing it or exposing secret values';

    private int $failures = 0;

    public function handle(): int
    {
        $root = realpath((string) ($this->option('path') ?: base_path()));
        if ($root === false || ! is_dir($root)) {
            $this->reportFail('release root', 'The requested release path does not exist.');

            return self::FAILURE;
        }

        $this->components->info('Commercial release audit (inspection only)');
        $files = $this->candidateFiles($root);
        $relativeFiles = array_keys($files);

        $this->checkRequiredFiles($root);
        $this->checkSensitiveFilenames($relativeFiles);
        $this->checkContent($files);
        $this->checkUnverifiedAssets($root);
        $this->checkWritableDirectories($root);
        $this->checkDebugMode();
        $this->checkPackagingDecisions($root);

        if (! $this->option('no-git')) {
            $this->checkGit($root);
        }

        $this->newLine();
        $this->line($this->failures === 0 ? '<fg=green>PASS</> No blocking failures found.' : "<fg=red>FAIL</> {$this->failures} blocking finding(s) found.");

        return $this->failures === 0 ? self::SUCCESS : self::FAILURE;
    }

    /** @return array<string, string> */
    private function candidateFiles(string $root): array
    {
        $excluded = config('commercial_release.excluded_directories', []);
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS));
        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
            if ($this->isExcluded($relative, $excluded)) {
                continue;
            }
            $files[$relative] = $file->getPathname();
        }
        ksort($files);

        return $files;
    }

    private function isExcluded(string $path, array $excluded): bool
    {
        foreach ($excluded as $directory) {
            $directory = trim((string) $directory, '/');
            if ($path === $directory || str_starts_with($path, $directory.'/')) {
                return true;
            }
        }

        return false;
    }

    private function checkRequiredFiles(string $root): void
    {
        foreach (config('commercial_release.required_files', []) as $file) {
            is_file($root.'/'.$file) ? $this->pass('required file', $file) : $this->reportFail('missing required file', $file);
        }
    }

    private function checkSensitiveFilenames(array $files): void
    {
        $extensions = config('commercial_release.blocking_extensions', []);
        foreach ($files as $file) {
            $name = basename($file);
            if (($name === '.env' || str_starts_with($name, '.env.')) && $name !== '.env.example') {
                $this->reportFail('environment file', $file);
            }
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($extension, $extensions, true)) {
                $this->reportFail('sensitive/generated extension', $file);
            }
        }
    }

    private function checkContent(array $files): void
    {
        foreach ($files as $relative => $absolute) {
            if (in_array($relative, config('commercial_release.excluded_content_files', []), true)) {
                continue;
            }
            if (@filesize($absolute) > 2_000_000 || ! $this->isTextFile($absolute)) {
                continue;
            }
            $lines = @file($absolute, FILE_IGNORE_NEW_LINES);
            if ($lines === false) {
                continue;
            }
            foreach ($lines as $index => $line) {
                foreach (config('commercial_release.sensitive_content_rules', []) as $rule => $pattern) {
                    if (preg_match($pattern, $line) === 1) {
                        $this->reportFail((string) $rule, "$relative: line ".($index + 1).' (content redacted)');
                    }
                }
                foreach (config('commercial_release.forbidden_branding', []) as $rule) {
                    if (! in_array($relative, config('commercial_release.branding_excluded_files', []), true)
                        && stripos($line, (string) $rule['term']) !== false) {
                        $message = "$relative: line ".($index + 1).' (branding context redacted)';
                        ($rule['severity'] ?? 'warning') === 'fail' ? $this->reportFail('forbidden branding', $message) : $this->warning('forbidden branding', $message);
                    }
                }
                foreach (config('commercial_release.production_domain_rules', []) as $pattern) {
                    if (preg_match($pattern, $line) === 1) {
                        $this->warning('production domain reference', "$relative: line ".($index + 1).' (context redacted)');
                    }
                }
                foreach (config('commercial_release.absolute_path_rules', []) as $pattern) {
                    if (preg_match($pattern, $line) === 1) {
                        $this->warning('internal absolute path', "$relative: line ".($index + 1).' (context redacted)');
                    }
                }
            }
        }
    }

    private function isTextFile(string $path): bool
    {
        $sample = @file_get_contents($path, false, null, 0, 1024);

        return $sample !== false && ! str_contains($sample, "\0");
    }

    private function checkUnverifiedAssets(string $root): void
    {
        foreach (config('commercial_release.unverified_assets', []) as $asset) {
            if (file_exists($root.'/'.$asset)) {
                $this->warning('unverified asset', $asset.' — MUST NOT SHIP until provenance is verified');
            }
        }
    }

    private function checkWritableDirectories(string $root): void
    {
        foreach (['storage', 'bootstrap/cache'] as $directory) {
            if (! is_dir($root.'/'.$directory)) {
                $this->warning('writable directory', "$directory is absent; create it and make it writable during deployment");
            } elseif (! is_writable($root.'/'.$directory)) {
                $this->warning('writable directory', "$directory must be writable by the PHP process");
            } else {
                $this->pass('writable directory', $directory);
            }
        }
    }

    private function checkDebugMode(): void
    {
        config('app.debug') ? $this->warning('debug mode', 'APP_DEBUG is enabled in the current environment; disable it for production') : $this->pass('debug mode', 'disabled');
    }

    private function checkPackagingDecisions(string $root): void
    {
        $vendor = config('commercial_release.packaging.vendor');
        $build = config('commercial_release.packaging.public_build');
        $this->warning('vendor packaging decision', (string) $vendor);
        is_file($root.'/public/build/manifest.json') ? $this->pass('compiled frontend assets', (string) $build) : $this->warning('compiled frontend assets', 'public/build is absent; the release workflow must run npm build');
    }

    private function checkGit(string $root): void
    {
        $process = new Process(['git', '-C', $root, 'ls-files']);
        $process->run();
        if (! $process->isSuccessful()) {
            $this->warning('Git checks', 'not a Git work tree or Git is unavailable');

            return;
        }
        $tracked = array_filter(explode("\n", trim($process->getOutput())));
        foreach ($tracked as $file) {
            if (preg_match('#^storage/logs/.+\.log$#i', $file)) {
                $this->reportFail('tracked runtime log', $file);
            }
            if (preg_match('#^storage/framework/views/.+\.php$#i', $file)) {
                $this->reportFail('tracked compiled Blade view', $file);
            }
            if (preg_match('/(?:^|\/)\.env(?:\.|$)/', $file) && basename($file) !== '.env.example') {
                $this->reportFail('tracked environment file', $file);
            }
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, ['sqlite', 'sqlite3', 'db', 'dump', 'pem', 'key', 'p12', 'pfx'], true)) {
                $this->reportFail('tracked sensitive file', $file);
            }
        }
        $status = new Process(['git', '-C', $root, 'status', '--porcelain']);
        $status->run();
        $status->isSuccessful() && trim($status->getOutput()) === '' ? $this->pass('Git cleanliness', 'working tree is clean') : $this->warning('Git cleanliness', 'working tree has changes or status is unavailable');
    }

    private function pass(string $rule, string $message): void
    {
        $this->line("<fg=green>PASS</> [$rule] $message");
    }

    private function warning(string $rule, string $message): void
    {
        $this->line("<fg=yellow>WARNING</> [$rule] $message");
    }

    private function reportFail(string $rule, string $message): void
    {
        $this->failures++;
        $this->line("<fg=red>FAIL</> [$rule] $message");
    }
}
