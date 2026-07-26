<?php

namespace App\Services\Release;

use FilesystemIterator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use ZipArchive;

class ReleaseBuilder
{
    public function __construct(private Filesystem $files, private ReleaseScanner $scanner) {}

    /** @return array<string,mixed> */
    public function build(string $variant, string $version, string $state, string $output, bool $dryRun, bool $archive, bool $keepStaging): array
    {
        $this->validate($variant, $version, $state, $output, $dryRun);
        $selected = $this->select($variant);
        if ($dryRun) {
            return ['variant' => $variant, 'files' => $selected, 'count' => count($selected), 'dry_run' => true];
        }

        $suffix = $state === 'internal_test' ? '-internal-test' : '';
        $name = config('commercial_release.product_slug').'-v'.$version.'-'.str_replace('_', '-', $variant).$suffix;
        $stagingRoot = $output.'/.staging-'.$name;
        if (file_exists($stagingRoot) || file_exists($output.'/'.$name.'.zip')) {
            throw new RuntimeException('Release output already exists; refusing to overwrite it.');
        }
        $this->files->ensureDirectoryExists($stagingRoot);
        file_put_contents($stagingRoot.'/.buildora-release-staging', "owned\n");
        try {
            foreach ($selected as $relative) {
                $destination = $stagingRoot.'/'.$relative;
                $this->files->ensureDirectoryExists(dirname($destination));
                if (! copy(base_path($relative), $destination)) {
                    throw new RuntimeException("Unable to stage {$relative}.");
                }
            }
            if ($state === 'internal_test') {
                file_put_contents($stagingRoot.'/NON-COMMERCIAL-NOT-FOR-DISTRIBUTION.txt', "INTERNAL TEST PACKAGE\nNON-COMMERCIAL — NOT FOR DISTRIBUTION\nThe final owner-approved LICENSE and acceptance gates may be incomplete.\n");
            }
            if ($variant === 'shared_hosting') {
                $this->installProductionVendor($stagingRoot);
            }
            $findings = $this->scanner->scan($stagingRoot);
            if ($findings !== []) {
                $first = $findings[0];
                throw new RuntimeException("Release audit blocked {$first['path']} ({$first['rule']}); content redacted.");
            }

            $inventory = $this->inventory($stagingRoot, $variant);
            file_put_contents($stagingRoot.'/file-inventory.json', $this->json($inventory));
            $manifest = $this->manifest($stagingRoot, $variant, $version, $state, $inventory);
            file_put_contents($stagingRoot.'/release-manifest.json', $this->json($manifest));
            file_put_contents($stagingRoot.'/release-report.md', $this->report($manifest));
            unlink($stagingRoot.'/.buildora-release-staging');

            $result = ['variant' => $variant, 'staging' => $stagingRoot, 'count' => count($inventory), 'size' => array_sum(array_column($inventory, 'size')), 'dry_run' => false];
            if ($archive) {
                $archivePath = $output.'/'.$name.'.zip';
                $this->archive($stagingRoot, $archivePath);
                $hash = hash_file(config('commercial_release.checksum_algorithm'), $archivePath);
                file_put_contents($archivePath.'.sha256', $hash.'  '.basename($archivePath)."\n");
                $result['archive'] = $archivePath;
                $result['checksum'] = $hash;
            }
            if (! $keepStaging) {
                $this->safeDelete($stagingRoot);
                unset($result['staging']);
            }

            return $result;
        } catch (\Throwable $exception) {
            if (is_file($stagingRoot.'/.buildora-release-staging')) {
                $this->safeDelete($stagingRoot);
            }
            throw $exception;
        }
    }

    /** @return list<string> */
    public function select(string $variant): array
    {
        $tracked = preg_split('/\R/', trim(Process::path(base_path())->run(['git', 'ls-files'])->throw()->output())) ?: [];
        $roots = config('commercial_release.source_roots');
        $single = config('commercial_release.source_files');
        $buyerDocs = config('commercial_release.buyer_documentation');
        $selected = [];
        foreach ($tracked as $path) {
            $path = str_replace('\\', '/', $path);
            $eligible = $variant === 'documentation'
                ? in_array($path, $buyerDocs, true)
                : in_array($path, $single, true) || in_array($path, $buyerDocs, true) || collect($roots)->contains(fn ($root) => $path === $root || str_starts_with($path, $root.'/'));
            if (! $eligible || $this->excluded($path)) {
                continue;
            }
            if (in_array($variant, ['source', 'shared_hosting'], true) && ! config("commercial_release.variants.{$variant}.tests") && str_starts_with($path, 'tests/')) {
                continue;
            }
            if (! is_file(base_path($path)) || is_link(base_path($path))) {
                continue;
            }
            $selected[] = $path;
        }
        sort($selected, SORT_STRING);

        return array_values(array_unique($selected));
    }

    private function validate(string $variant, string $version, string $state, string &$output, bool $dryRun): void
    {
        if (! isset(config('commercial_release.variants')[$variant])) {
            throw new RuntimeException('Unknown release variant.');
        }
        if (! preg_match('/^[0-9]+\.[0-9]+\.[0-9]+(?:-[0-9A-Za-z.-]+)?$/', $version)) {
            throw new RuntimeException('Version must be a semantic version without path characters.');
        }
        if (! in_array($state, config('commercial_release.approval_states'), true)) {
            throw new RuntimeException('Unknown approval state.');
        }
        $output = $this->safeOutputPath($output, ! $dryRun);
        if (! $dryRun && trim(Process::path(base_path())->run(['git', 'status', '--porcelain'])->output()) !== '') {
            throw new RuntimeException('Archive builds require a clean Git working tree.');
        }
        if (! $dryRun && $state !== 'internal_test' && ! is_file(base_path('LICENSE'))) {
            throw new RuntimeException('Final owner-approved LICENSE is missing. Public release is blocked.');
        }
        if ($state === 'commercially_approved') {
            foreach (config('commercial_release.acceptance_reports') as $report) {
                $definition = is_string($report) ? ['path' => $report] : $report;
                $path = $definition['path'] ?? null;
                $data = is_string($path) && is_file(base_path($path))
                    ? json_decode((string) file_get_contents(base_path($path)), true)
                    : null;
                $statusField = $definition['status_field'] ?? 'status';
                if (strtoupper((string) ($data[$statusField] ?? '')) !== 'PASSED') {
                    throw new RuntimeException('Commercial approval requires structured passed acceptance reports.');
                }
            }
            throw new RuntimeException('Commercial approval cannot be selected automatically; owner packaging and provenance approval is still required.');
        }
    }

    private function safeOutputPath(string $path, bool $create): string
    {
        if ($path === '' || str_contains($path, "\0")) {
            throw new RuntimeException('Invalid output path.');
        }
        $absolute = str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);
        $absolute = rtrim($absolute, DIRECTORY_SEPARATOR);
        $parent = realpath(dirname($absolute));
        if ($parent === false || basename($absolute) === '..' || str_contains($absolute, '/../')) {
            throw new RuntimeException('Output parent must exist and path traversal is forbidden.');
        }
        $resolved = $parent.DIRECTORY_SEPARATOR.basename($absolute);
        foreach ([base_path('storage'), base_path('bootstrap/cache'), public_path()] as $protected) {
            if ($resolved === $protected || str_starts_with($resolved, $protected.DIRECTORY_SEPARATOR)) {
                throw new RuntimeException('Output path is inside a protected runtime directory.');
            }
        }
        if ($create) {
            $this->files->ensureDirectoryExists($resolved);
        }

        return $resolved;
    }

    private function excluded(string $path): bool
    {
        foreach (config('commercial_release.excluded_paths') as $excluded) {
            if ($path === $excluded || str_starts_with($path, $excluded.'/')) {
                return ! in_array($path, config('commercial_release.allowed_runtime_placeholders'), true);
            }
        }

        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), config('commercial_release.excluded_extensions'), true);
    }

    private function installProductionVendor(string $staging): void
    {
        if (! is_dir(base_path('vendor')) || ! $this->files->copyDirectory(base_path('vendor'), $staging.'/vendor')) {
            throw new RuntimeException('A local dependency tree is required for the isolated shared-hosting build.');
        }
        $result = Process::path($staging)->env(['COMPOSER_DISABLE_NETWORK' => '1'])->timeout(600)->run(['composer', 'install', '--no-dev', '--optimize-autoloader', '--classmap-authoritative', '--no-interaction', '--no-scripts']);
        if (! $result->successful() || ! is_file($staging.'/vendor/autoload.php')) {
            throw new RuntimeException('Isolated production dependency installation failed.');
        }
    }

    /** @return list<array{path:string,size:int,sha256:string,variant:string}> */
    private function inventory(string $root, string $variant): array
    {
        $inventory = [];
        foreach ($this->stagedFiles($root) as [$relative, $absolute]) {
            if (in_array($relative, ['file-inventory.json', 'release-manifest.json', 'release-report.md', '.buildora-release-staging'], true)) {
                continue;
            }
            $inventory[] = ['path' => $relative, 'size' => filesize($absolute), 'sha256' => hash_file('sha256', $absolute), 'variant' => $variant];
        }
        usort($inventory, fn ($a, $b) => strcmp($a['path'], $b['path']));

        return $inventory;
    }

    /** @param list<array<string,mixed>> $inventory @return array<string,mixed> */
    private function manifest(string $root, string $variant, string $version, string $state, array $inventory): array
    {
        $docs = array_values(array_filter(config('commercial_release.buyer_documentation'), fn ($path) => is_file($root.'/'.$path)));
        sort($docs);

        return [
            'product_name' => config('commercial_release.product_name'), 'product_slug' => config('commercial_release.product_slug'),
            'version' => $version, 'edition' => 'commercial', 'variant' => $variant,
            'license' => config('commercial_release.license_type'), 'licensor' => config('commercial_release.licensor'),
            'build_timestamp' => gmdate('Y-m-d\TH:i:s\Z'), 'source_commit' => trim(Process::path(base_path())->run(['git', 'rev-parse', 'HEAD'])->throw()->output()),
            'php_requirement' => '^8.2', 'laravel_version' => app()->version(), 'package_file_count' => count($inventory),
            'total_uncompressed_size' => array_sum(array_column($inventory, 'size')), 'included_documentation' => $docs,
            'public_build_included' => is_file($root.'/public/build/manifest.json'), 'vendor_included' => is_file($root.'/vendor/autoload.php'),
            'demo_assets_included' => false, 'installer_included' => is_file($root.'/routes/install.php'),
            'license_provider_support' => config('commercial_release.supported_license_providers'),
            'operational_providers' => config('commercial_release.operational_license_providers'),
            'checksum_algorithm' => config('commercial_release.checksum_algorithm'), 'release_approval_status' => $state,
        ];
    }

    /** @param array<string,mixed> $manifest */
    private function report(array $manifest): string
    {
        $license = is_file(base_path('LICENSE')) ? 'proprietary; owner-approved final file included' : 'BLOCKED — final owner-approved LICENSE absent';

        return "# Release Report\n\n- Product/version: {$manifest['product_name']} {$manifest['version']}\n- Variant: {$manifest['variant']}\n- Source commit: {$manifest['source_commit']}\n- Build date (UTC): {$manifest['build_timestamp']}\n- Approval state: {$manifest['release_approval_status']}\n- Release audit: passed for staged files\n- Automated test summary: not asserted by builder\n- MySQL acceptance: pending\n- Fresh ZIP installation: pending\n- Asset provenance: verified build only; uploads and favicon excluded\n- Vendor decision: ".config('commercial_release.packaging.vendor')."\n- Public build decision: ".config('commercial_release.packaging.public_build')."\n- License: {$license}\n- Licensor: {$manifest['licensor']}\n- Known limitations: no automatic updater; only offline licensing is operational; commercial approval gates remain manual\n- Archive checksum: generated as an external SHA-256 sidecar\n- Inventory file count: {$manifest['package_file_count']}\n- Uncompressed inventory size: {$manifest['total_uncompressed_size']} bytes\n";
    }

    private function archive(string $root, string $destination): void
    {
        $zip = new ZipArchive;
        if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
            throw new RuntimeException('Unable to create ZIP archive.');
        }
        foreach ($this->stagedFiles($root) as [$relative, $absolute]) {
            $zip->addFile($absolute, $relative);
            $zip->setMtimeName($relative, 315532800);
        }
        $zip->close();
    }

    /** @return list<array{string,string}> */
    private function stagedFiles(string $root): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $entry) {
            if ($entry->isFile() && ! $entry->isLink()) {
                $relative = str_replace('\\', '/', substr($entry->getPathname(), strlen(rtrim($root, DIRECTORY_SEPARATOR)) + 1));
                $files[] = [$relative, $entry->getPathname()];
            }
        }
        usort($files, fn ($a, $b) => strcmp($a[0], $b[0]));

        return $files;
    }

    private function safeDelete(string $path): void
    {
        if (! str_contains(basename($path), '.staging-buildora-cms-') || ! str_starts_with(realpath(dirname($path)) ?: '', DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('Refusing unsafe staging cleanup.');
        }
        $this->files->deleteDirectory($path);
    }

    /** @param mixed $value */
    private function json($value): string
    {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
    }
}
