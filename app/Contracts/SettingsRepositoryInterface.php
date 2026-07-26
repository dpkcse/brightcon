<?php

namespace App\Contracts;

use App\Models\SiteSetting;
use App\Models\ThemeSetting;

interface SettingsRepositoryInterface
{
    public function site(): SiteSetting;

    public function theme(): ThemeSetting;

    public function get(string $key, mixed $default = null): mixed;

    public function string(string $key, ?string $default = null): ?string;

    public function bool(string $key, bool $default = false): bool;

    public function int(string $key, ?int $default = null): ?int;

    public function decimal(string $key, mixed $default = null): mixed;

    public function url(string $key, ?string $default = null): ?string;

    public function color(string $key, ?string $default = null): ?string;

    public function forgetSiteCache(): void;

    public function forgetThemeCache(): void;

    public function forgetFrontendCache(): void;

    public function refresh(): void;
}
