<?php

namespace App\Services\Installation;

use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class EnvironmentWriter
{
    public function write(array $values, bool $allowExisting = false): void
    {
        $path = base_path('.env');
        if (is_file($path) && ! $allowExisting) {
            throw new RuntimeException('An environment file already exists. Review it manually or explicitly approve an update.');
        }
        $contents = is_file($path) ? file_get_contents($path) : file_get_contents(base_path('.env.example'));
        if ($contents === false) {
            throw new RuntimeException('The environment template could not be read.');
        }
        foreach ($values as $key => $value) {
            if (! preg_match('/^[A-Z][A-Z0-9_]*$/', $key) || str_contains((string) $value, "\n") || str_contains((string) $value, "\r")) {
                throw new InvalidArgumentException('An environment value is malformed.');
            }
            $encoded = '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], (string) $value).'"';
            $line = $key.'='.$encoded;
            $pattern = '/^'.preg_quote($key, '/').'=.*$/m';
            $contents = preg_match($pattern, $contents) ? preg_replace($pattern, $line, $contents) : rtrim($contents).PHP_EOL.$line.PHP_EOL;
        }
        if (is_file($path) && ! copy($path, $path.'.backup-'.gmdate('YmdHis'))) {
            throw new RuntimeException('The environment backup could not be created.');
        }
        $temporary = $path.'.'.Str::random(12).'.tmp';
        if (file_put_contents($temporary, $contents, LOCK_EX) === false || ! rename($temporary, $path)) {
            @unlink($temporary);
            throw new RuntimeException('The environment file could not be written.');
        }
        @chmod($path, 0600);
    }

    public function validKey(?string $key): bool
    {
        if (! str_starts_with((string) $key, 'base64:')) {
            return false;
        }

        return strlen((string) base64_decode(substr($key, 7), true)) === 32;
    }

    public function generateKey(): string
    {
        return 'base64:'.base64_encode(random_bytes(32));
    }
}
