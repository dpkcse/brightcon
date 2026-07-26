<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\ThemeSetting;
use App\Services\Settings\SettingsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $created = DB::transaction(function (): bool {
            $siteCreated = false;
            $themeCreated = false;

            if (! SiteSetting::query()->exists()) {
                SiteSetting::query()->create($this->defaultsFor(new SiteSetting));
                $siteCreated = true;
            }

            if (! ThemeSetting::query()->exists()) {
                ThemeSetting::query()->create($this->defaultsFor(new ThemeSetting));
                $themeCreated = true;
            }

            return $siteCreated || $themeCreated;
        });

        if ($created) {
            app(SettingsService::class)->refresh();
        }
    }

    private function defaultsFor(SiteSetting|ThemeSetting $model): array
    {
        return array_intersect_key(config('cms.defaults', []), array_flip($model->getFillable()));
    }
}
