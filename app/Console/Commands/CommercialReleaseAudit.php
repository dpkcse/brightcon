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
        // Artisan command instances may be reused by the test runner or an
        // embedding process; findings from an earlier invocation must not leak.
        $this->failures = 0;
        $root = realpath((string) ($this->option('path') ?: base_path()));
        if ($root === false || ! is_dir($root)) {
            $this->reportFail('release root', 'The requested release path does not exist.');

            return self::FAILURE;
        }

        $this->components->info('Commercial release audit (inspection only)');
        $files = $this->candidateFiles($root);
        $relativeFiles = array_keys($files);

        $this->checkRequiredFiles($root);
        $this->checkFinalLicenseStatus($root);
        $this->checkComposerLicense($root);
        $this->checkSensitiveFilenames($relativeFiles);
        $this->checkContent($files);
        $this->checkUnverifiedAssets($root);
        $this->checkWritableDirectories($root);
        $this->checkDebugMode();
        $this->checkPackagingDecisions($root);
        $this->checkLicenseEnforcement($root);
        $this->checkAcceptanceReports($root);
        $this->checkReleaseFoundation($root);

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

    private function checkFinalLicenseStatus(string $root): void
    {
        if (! in_array('LICENSE', config('commercial_release.required_files', []), true) || ! is_file($root.'/LICENSE')) {
            return;
        }

        $license = (string) file_get_contents($root.'/LICENSE');
        $identity = config('commercial_release.license_identity');
        if (preg_match('/^'.preg_quote($identity['status'], '/').'\h*$/m', $license) !== 1) {
            $this->reportFail('final license approval', 'LICENSE is not marked OWNER-APPROVED FINAL LICENSE');

            return;
        }

        if (preg_match('/\[[^\]]*(?:placeholder|must complete|to complete)[^\]]*\]|TODO[^\r\n]*license/i', $license) === 1) {
            $this->reportFail('final license approval', 'LICENSE contains unresolved owner/legal placeholders');

            return;
        }

        $required = [
            'licensor name' => '/Licensor:\h*'.preg_quote($identity['licensor'], '/').'/i',
            'registered address' => '/Registered address:\h*'.preg_quote($identity['address'], '/').'/i',
            'legal contact' => '/License contact:\h*'.preg_quote($identity['contact'], '/').'/i',
            'governing law' => '/governed by '.preg_quote(strtolower($identity['governing_law']), '/').'/i',
            'jurisdiction' => '/'.preg_quote($identity['jurisdiction'], '/').'/i',
            'effective date' => '/License version:[^\r\n]*Effective date:\h*'.preg_quote($identity['effective_date'], '/').'/i',
            'authorized signatory' => '/Authorized signatory:\h*'.preg_quote($identity['signatory'], '/').'/i',
            'approval date' => '/Approval date:\h*'.preg_quote($identity['approval_date'], '/').'/i',
        ];
        $normalizedLicense = preg_replace('/\s+/', ' ', $license);
        foreach ($required as $field => $pattern) {
            if (preg_match($pattern, $normalizedLicense) !== 1) {
                $this->reportFail('final license approval', "LICENSE is missing approved {$field}");

                return;
            }
        }

        $draft = $root.'/LICENSE-DRAFT.md';
        if (is_file($draft) && ! str_contains((string) file_get_contents($draft), 'SUPERSEDED — THE AUTHORITATIVE LICENSE IS THE ROOT LICENSE FILE')) {
            $this->reportFail('draft license disposition', 'LICENSE-DRAFT.md is not prominently superseded');

            return;
        }

        $this->pass('final license approval', 'owner-approved identity, status, sign-off, and placeholder checks passed');
    }

    private function checkComposerLicense(string $root): void
    {
        $path = $root.'/composer.json';
        if (! is_file($path)) {
            $this->reportFail('composer license', 'composer.json is missing');

            return;
        }
        $composer = json_decode((string) file_get_contents($path), true);
        ($composer['license'] ?? null) === 'proprietary'
            ? $this->pass('composer license', 'proprietary')
            : $this->reportFail('composer license', 'composer.json must declare proprietary licensing');
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

    private function checkLicenseEnforcement(string $root): void
    {
        $configuration = $root.'/config/licensing.php';
        if (! is_file($configuration)) {
            return;
        }

        $contents = (string) file_get_contents($configuration);
        foreach (['public_site_requires_valid_license', 'backup_requires_valid_license', 'export_requires_valid_license'] as $setting) {
            preg_match("/'{$setting}'\\s*=>\\s*(true|false)/", $contents, $match);
            ($match[1] ?? null) === 'false'
                ? $this->pass('safe license default', "$setting is false")
                : $this->reportFail('unsafe license lockout default', $setting);
        }

        foreach (['routes/web.php', 'routes/admin.php'] as $routeFile) {
            $routeContents = is_file($root.'/'.$routeFile) ? (string) file_get_contents($root.'/'.$routeFile) : '';
            preg_match('/license\\.(?:valid|required)|RequireValidLicense/', $routeContents) === 1
                ? $this->reportFail('blanket license middleware', $routeFile)
                : $this->pass('route license safety', $routeFile);
        }

        config('licensing.offline.public_key')
            ? $this->pass('offline verification key', 'configured')
            : $this->warning('offline verification key', 'missing; offline activation requires configuration');
        $this->warning('marketplace provider', 'No marketplace adapter is operational; no external verification was performed');
    }

    private function checkReleaseFoundation(string $root): void
    {
        if ($root !== realpath(base_path())) {
            return;
        }
        is_file($root.'/app/Console/Commands/CmsReleaseCommand.php')
            ? $this->pass('release builder', 'cms:release command exists')
            : $this->reportFail('release builder', 'cms:release command is missing');
        config('commercial_release.variants.source') && config('commercial_release.variants.shared_hosting') && config('commercial_release.variants.documentation')
            ? $this->pass('release configuration', 'all package variants are configured')
            : $this->reportFail('release configuration', 'package variant policy is incomplete');
        foreach (['documentation/read-me-first.md', 'documentation/single-site-license.md', 'documentation/update-policy.md', 'documentation/support-policy.md', 'documentation/refund-policy.md'] as $document) {
            is_file($root.'/'.$document) ? $this->pass('buyer documentation', $document) : $this->reportFail('buyer documentation', $document);
        }
        $ignore = is_file($root.'/.gitignore') ? (string) file_get_contents($root.'/.gitignore') : '';
        str_contains($ignore, '/release/') ? $this->pass('release output', 'release directory is ignored') : $this->reportFail('release output', 'release directory is not ignored');
    }

    private function checkAcceptanceReports(string $root): void
    {
        foreach (config('commercial_release.acceptance_reports', []) as $gate => $definition) {
            $definition = is_string($definition) ? ['path' => $definition, 'required' => ['schema_version', 'status']] : $definition;
            $path = $definition['path'] ?? null;
            if (! is_string($path) || ! is_file($root.'/'.$path)) {
                $this->reportFail("{$gate} acceptance", 'structured report is missing');

                continue;
            }

            try {
                $data = json_decode((string) file_get_contents($root.'/'.$path), true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $this->reportFail("{$gate} acceptance", 'report is not valid JSON');

                continue;
            }
            if (! is_array($data)) {
                $this->reportFail("{$gate} acceptance", 'report root must be an object');

                continue;
            }
            $missing = array_values(array_filter($definition['required'] ?? [], fn ($field) => ! array_key_exists($field, $data)));
            if ($missing !== []) {
                $this->reportFail("{$gate} acceptance", 'missing required fields: '.implode(', ', $missing));

                continue;
            }
            $statusField = $definition['status_field'] ?? 'status';
            $status = strtoupper((string) ($data[$statusField] ?? ''));
            if ($status !== 'PASSED') {
                $this->reportFail("{$gate} acceptance", 'status is '.($status ?: 'missing').'; commercial approval remains blocked');

                continue;
            }
            $this->pass("{$gate} acceptance", 'structured report passed');
        }
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
