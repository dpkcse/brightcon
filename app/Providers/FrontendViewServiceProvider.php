<?php

namespace App\Providers;

use App\Models\FooterLink;
use App\Models\MenuItem;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\ThemeSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FrontendViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('frontend.*', function ($view): void {
            $view->with([
                'siteSettings' => Cache::rememberForever('site_settings', fn () => SiteSetting::query()->first()),
                'themeSettings' => Cache::rememberForever('theme_settings', fn () => ThemeSetting::query()->first()),
                'socialLinks' => Cache::rememberForever('social_links_active_ordered', fn () => SocialLink::query()->active()->ordered()->get()),
                'menuItems' => Cache::rememberForever('menu_items_active_ordered', fn () => MenuItem::query()->active()->ordered()->get()),
                'footerLinks' => Cache::rememberForever('footer_links_active_ordered', fn () => FooterLink::query()->active()->ordered()->get()),
            ]);
        });
    }
}
