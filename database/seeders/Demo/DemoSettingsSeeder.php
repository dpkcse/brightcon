<?php

namespace Database\Seeders\Demo;

use App\Models\SiteSetting;
use App\Services\Settings\SettingsService;
use Illuminate\Database\Seeder;

class DemoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $setting = SiteSetting::query()->first();

        if (! $setting) {
            $setting = SiteSetting::query()->create($this->values());
            app(SettingsService::class)->forgetSiteCache();

            return;
        }

        if ($this->isUntouchedCleanDefault($setting)) {
            $setting->update($this->values());
            app(SettingsService::class)->forgetSiteCache();
        }
    }

    private function isUntouchedCleanDefault(SiteSetting $setting): bool
    {
        $defaults = config('cms.defaults');

        return $setting->company_name === ($defaults['company_name'] ?? null)
            && $setting->tagline === ($defaults['tagline'] ?? null)
            && blank($setting->email)
            && blank($setting->phone)
            && blank($setting->address)
            && blank($setting->logo)
            && blank($setting->favicon)
            && blank($setting->installation_completed_at);
    }

    private function values(): array
    {
        return [
            'company_name' => 'Buildora Construction & Engineering',
            'company_short_name' => 'Buildora Engineering',
            'tagline' => 'Building Strong Foundations for the Future',
            'company_description' => 'Buildora Construction & Engineering is a fictional demonstration company used to showcase the features of Buildora CMS.',
            'email' => 'info@example.com',
            'secondary_email' => null,
            'phone' => '+1 202-555-0147',
            'secondary_phone' => '+1 202-555-0189',
            'address' => '100 Example Avenue, Metro City',
            'secondary_address' => 'Project Office, 25 Sample Road, Metro City',
            'show_contact_map' => false,
            'google_map_embed_url' => null,
            'default_language' => 'en',
            'timezone' => 'UTC',
            'copyright_text' => 'Buildora Construction & Engineering. Fictional demonstration content.',
        ];
    }
}
