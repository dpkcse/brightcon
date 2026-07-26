<?php

namespace App\Support;

class GoogleMapsUrl
{
    private const TRUSTED_HOSTS = [
        'google.com',
        'www.google.com',
        'maps.google.com',
        'googleusercontent.com',
    ];

    public static function extractEmbedUrl(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/<iframe\b[^>]*\bsrc\s*=\s*(["\'])(.*?)\1/is', $value, $matches)) {
            $value = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return trim($value);
    }

    public static function isTrustedEmbedUrl(?string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower(rtrim((string) parse_url($url, PHP_URL_HOST), '.'));

        return $scheme === 'https' && in_array($host, self::TRUSTED_HOSTS, true);
    }
}
