<?php

namespace App\Providers;

use App\Contracts\SettingsRepositoryInterface;
use App\Models\FooterLink;
use App\Models\MenuItem;
use App\Models\SocialLink;
use App\Services\Settings\SettingsService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FrontendViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(SettingsRepositoryInterface::class, fn () => new SettingsService);
    }

    public function boot(): void
    {
        View::composer('frontend.*', function ($view): void {
            $settings = app(SettingsRepositoryInterface::class);
            $view->with([
                'siteSettings' => $settings->site(), 'themeSettings' => $settings->theme(),
                'socialLinks' => $this->safeCollection('social_links', 'social_links_active_ordered', fn () => SocialLink::query()->active()->ordered()->get()),
                'menuItems' => $this->safeCollection('menu_items', 'menu_items_active_ordered', fn () => MenuItem::query()->with(['page', 'children.page'])->active()->ordered()->get()),
                'footerLinks' => $this->safeCollection('footer_links', 'footer_links_active_ordered', fn () => FooterLink::query()->active()->ordered()->get()),
            ]);
        });
    }

    private function safeCollection(string $table, string $key, callable $query)
    {
        try {
            if (! Schema::hasTable($table)) {
                return collect();
            }

            return Cache::rememberForever($key, $query);
        } catch (QueryException $e) {
            if (preg_match('/(no such table|doesn.t exist|connection|unable to open database)/i', $e->getMessage())) {
                return collect();
            } throw $e;
        }
    }
}
