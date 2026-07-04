<?php

namespace App\Support;

class SafeCmsUrl
{
    public static function rules(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'string',
            'max:255',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (blank($value)) {
                    return;
                }

                if (! self::isSafe((string) $value)) {
                    $fail('The '.$attribute.' must be a safe relative path, anchor, or http(s) URL.');
                }
            },
        ];
    }

    public static function isSafe(string $url): bool
    {
        $url = trim($url);

        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '#')) {
            return ! preg_match('/[\x00-\x1F\x7F]/', $url);
        }

        if (str_starts_with($url, '/')) {
            return ! str_starts_with($url, '//') && ! preg_match('/[\x00-\x1F\x7F]/', $url);
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        return in_array(strtolower((string) $scheme), ['http', 'https'], true)
            && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
