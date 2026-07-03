<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UniqueSlug
{
    public static function make(string $table, string $source, ?int $ignoreId = null, string $column = 'slug'): string
    {
        $base = Str::slug(Str::lower($source));
        $base = $base !== '' ? $base : 'item';
        $slug = $base;
        $counter = 2;

        while (self::exists($table, $column, $slug, $ignoreId)) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private static function exists(string $table, string $column, string $slug, ?int $ignoreId): bool
    {
        return DB::table($table)
            ->where($column, $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
