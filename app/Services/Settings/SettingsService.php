<?php

namespace App\Services\Settings;

use App\Contracts\SettingsRepositoryInterface;
use App\Models\SiteSetting;
use App\Models\ThemeSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

class SettingsService implements SettingsRepositoryInterface
{
    private ?SiteSetting $site = null;

    private ?ThemeSetting $theme = null;

    public function site(): SiteSetting
    {
        return $this->site ??= $this->load(SiteSetting::class, 'site');
    }

    public function theme(): ThemeSetting
    {
        return $this->theme ??= $this->load(ThemeSetting::class, 'theme');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $fallback = config('cms.defaults.'.$key, $default);
        $value = in_array($key, (new ThemeSetting)->getFillable(), true) ? $this->theme()->getAttribute($key) : $this->site()->getAttribute($key);

        return $value ?? $fallback;
    }

    public function string(string $key, ?string $default = null): ?string
    {
        $v = $this->get($key, $default);

        return $v === null ? null : (string) $v;
    }

    public function bool(string $key, bool $default = false): bool
    {
        return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOL);
    }

    public function int(string $key, ?int $default = null): ?int
    {
        $v = $this->get($key, $default);

        return is_numeric($v) ? (int) $v : $default;
    }

    public function decimal(string $key, mixed $default = null): mixed
    {
        $v = $this->get($key, $default);

        return is_numeric($v) ? $v : $default;
    }

    public function url(string $key, ?string $default = null): ?string
    {
        $v = $this->string($key, $default);

        return $v && filter_var($v, FILTER_VALIDATE_URL) ? $v : $default;
    }

    public function color(string $key, ?string $default = null): ?string
    {
        $v = $this->string($key, $default);

        return $v && preg_match('/^#[0-9A-F]{6}$/i', $v) ? strtoupper($v) : $default;
    }

    public function forgetSiteCache(): void
    {
        $this->site = null;
        $this->forget(['site_settings', config('cms.cache.site')]);
        $this->forgetFrontendCache();
    }

    public function forgetThemeCache(): void
    {
        $this->theme = null;
        $this->forget(['theme_settings', config('cms.cache.theme')]);
        $this->forgetFrontendCache();
    }

    public function forgetFrontendCache(): void
    {
        $this->forget([config('cms.cache.frontend'), config('cms.cache.seo')]);
    }

    public function refresh(): void
    {
        $this->forgetSiteCache();
        $this->forgetThemeCache();
    }

    private function load(string $model, string $type): SiteSetting|ThemeSetting
    {
        $defaults = array_intersect_key(config('cms.defaults', []), array_flip((new $model)->getFillable()));
        try {
            try {
                $row = Cache::rememberForever($type.'_settings', fn () => $model::query()->first());
            } catch (QueryException $e) {
                if (! $this->isInstallState($e)) {
                    throw $e;
                } $row = $model::query()->first();
            }

            return $row ?: new $model($defaults);
        } catch (QueryException $e) {
            if (! $this->isInstallState($e)) {
                throw $e;
            }

            return new $model($defaults);
        }
    }

    private function isInstallState(QueryException $e): bool
    {
        $state = (string) ($e->errorInfo[0] ?? '');
        $message = strtolower($e->getMessage());

        return in_array($state, ['08001', '08004', '08006', 'HY000', '42S02', '42P01'], true)
            && preg_match('/(no such table|doesn.t exist|undefined table|connection|database.*not found|unable to open database)/', $message);
    }

    private function forget(array $keys): void
    {
        foreach (array_filter(array_unique($keys)) as $key) {
            try {
                Cache::forget($key);
            } catch (QueryException $e) {
                if (! $this->isInstallState($e)) {
                    throw $e;
                }
            }
        }
    }
}
