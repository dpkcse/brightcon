<?php

namespace App\Services\Installation;

class PermissionChecker
{
    public function check(bool $createSafeDirectories = false): array
    {
        $paths = ['storage', 'storage/app', 'storage/framework', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs', 'bootstrap/cache'];

        return collect($paths)->map(function (string $path) use ($createSafeDirectories): array {
            $absolute = base_path($path);
            if ($createSafeDirectories && ! is_dir($absolute)) {
                @mkdir($absolute, 0755, true);
            }

            return ['path' => $path, 'exists' => is_dir($absolute), 'writable' => is_dir($absolute) && is_writable($absolute), 'passed' => is_dir($absolute) && is_writable($absolute)];
        })->all();
    }

    public function passes(): bool
    {
        return collect($this->check())->every('passed');
    }

    public function storageLinkStatus(): string
    {
        return is_link(public_path('storage')) ? 'linked' : (file_exists(public_path('storage')) ? 'path_exists' : 'missing');
    }
}
