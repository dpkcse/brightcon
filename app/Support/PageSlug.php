<?php

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class PageSlug
{
    public const RESERVED = ['admin', 'install', 'login', 'about', 'services', 'projects', 'gallery', 'contact', 'competency', 'equipment-list', 'pages', 'sitemap.xml', 'robots.txt', 'storage', 'up'];

    public static function normalize(string $value): string
    {
        return Str::slug($value);
    }

    public static function assertAllowed(string $slug): void
    {
        if ($slug === '' || in_array(strtolower($slug), self::RESERVED, true)) {
            throw ValidationException::withMessages(['slug' => 'This slug is reserved for a system route.']);
        }
    }
}
