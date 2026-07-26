<?php

namespace App\Providers;

use App\Licensing\LicenseManager;
use App\Licensing\ProviderRegistry;
use Illuminate\Support\ServiceProvider;

class LicensingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProviderRegistry::class);
        $this->app->singleton(LicenseManager::class);
    }
}
