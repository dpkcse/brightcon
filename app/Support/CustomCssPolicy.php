<?php

namespace App\Support;

class CustomCssPolicy
{
    public const MAX_LENGTH = 20000;

    public static function isSafe(?string $css): bool
    {
        if (! filled($css)) {
            return true;
        }
        if (strlen($css) > self::MAX_LENGTH || preg_match('/[<>]/', $css)) {
            return false;
        }

        return ! preg_match('/@import|url\s*\(|expression\s*\(|javascript\s*:|data\s*:|behavior\s*:|-moz-binding|<\/\s*style/i', $css);
    }
}
