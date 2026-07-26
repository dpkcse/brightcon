<?php

namespace App\Services\Release;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ReleaseScanner
{
    /** @return list<array{rule:string,path:string,severity:string,line:?int,context:string}> */
    public function scan(string $root): array
    {
        $findings = [];
        $forbiddenNames = ['.env', '.git', '.idea', '.vscode', '.installed', '.installation-partial', '.license-installation-id'];
        $forbiddenExtensions = array_merge(config('commercial_release.excluded_extensions', []), ['zip']);

        foreach ($this->files($root) as $path) {
            $relative = str_replace('\\', '/', substr($path, strlen(rtrim($root, DIRECTORY_SEPARATOR)) + 1));
            $parts = explode('/', $relative);
            $extension = strtolower(pathinfo($relative, PATHINFO_EXTENSION));

            if (array_intersect($parts, $forbiddenNames) || in_array($extension, $forbiddenExtensions, true)) {
                $findings[] = $this->finding('forbidden path or file type', $relative);

                continue;
            }
            if (is_link($path)) {
                $findings[] = $this->finding('symlink is not permitted', $relative);

                continue;
            }
            if (filesize($path) > 2_000_000 || $this->isBinary($path)) {
                continue;
            }
            // Composer packages are lock-file selected and separately license-inventoried.
            // Their examples may contain harmless development paths or token terminology.
            if (str_starts_with($relative, 'vendor/')) {
                continue;
            }

            $contents = (string) file_get_contents($path);
            $rules = [
                'private key' => '/-----BEGIN (?:RSA |EC |OPENSSH )?PRIVATE KEY-----/',
                'populated application key' => '/^APP_KEY[ \t]*=[ \t]*(?!["\']?[ \t]*$).+/mi',
                'bearer token' => '/\bBearer\s+[A-Za-z0-9._~+\/-]{12,}/i',
                'local absolute path' => '#(?:/workspace/|/home/[^/\s]+/|[A-Z]:\\\\Users\\\\)#',
                'former product branding' => '/\bBrightCon\b|Bright Construction|brightconeng\.com/i',
            ];
            foreach ($rules as $rule => $pattern) {
                if ($relative === 'app/Services/Release/ReleaseScanner.php') {
                    continue;
                }
                if ($rule === 'former product branding' && in_array($relative, ['config/commercial_release.php', 'app/Services/Release/ReleaseScanner.php'], true)) {
                    continue;
                }
                if (preg_match($pattern, $contents, $match, PREG_OFFSET_CAPTURE)) {
                    $line = substr_count(substr($contents, 0, $match[0][1]), "\n") + 1;
                    $findings[] = $this->finding($rule, $relative, $line);
                }
            }
        }

        return $findings;
    }

    /** @return list<string> */
    private function files(string $root): array
    {
        if (! is_dir($root)) {
            return [];
        }
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $entry) {
            if ($entry->isFile() || $entry->isLink()) {
                $files[] = $entry->getPathname();
            }
        }
        sort($files, SORT_STRING);

        return $files;
    }

    private function isBinary(string $path): bool
    {
        $sample = (string) file_get_contents($path, false, null, 0, 4096);

        return str_contains($sample, "\0");
    }

    /** @return array{rule:string,path:string,severity:string,line:?int,context:string} */
    private function finding(string $rule, string $path, ?int $line = null): array
    {
        return ['rule' => $rule, 'path' => $path, 'severity' => 'blocking', 'line' => $line, 'context' => '[REDACTED]'];
    }
}
