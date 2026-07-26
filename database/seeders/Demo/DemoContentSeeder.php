<?php

namespace Database\Seeders\Demo;

use App\Models\FeatureItem;
use App\Models\GalleryImage;
use App\Models\Organization;
use App\Models\PartnerMessage;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\Slider;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->guardProduction();
        $this->guardCustomerContent();

        $this->call([
            DemoSettingsSeeder::class,
            DemoServiceSeeder::class,
            DemoProjectSeeder::class,
            DemoOrganizationSeeder::class,
            DemoSliderSeeder::class,
            DemoFeatureItemSeeder::class,
            DemoPartnerMessageSeeder::class,
            DemoPhaseHSeeder::class,
        ]);
    }

    private function guardProduction(): void
    {
        if (app()->environment('production') && ! $this->acknowledged('CMS_ALLOW_DEMO_SEED_IN_PRODUCTION')) {
            throw new RuntimeException('Demo seeding in production requires CMS_ALLOW_DEMO_SEED_IN_PRODUCTION=true.');
        }
    }

    private function guardCustomerContent(): void
    {
        $hasCustomerContent = Service::query()->whereNotIn('slug', DemoServiceSeeder::slugs())->exists()
            || ProjectCategory::query()->whereNotIn('slug', DemoProjectSeeder::categorySlugs())->exists()
            || Project::query()->whereNotIn('slug', DemoProjectSeeder::projectSlugs())->exists()
            || Organization::query()->whereNotIn('name', DemoOrganizationSeeder::names())->exists()
            || Slider::query()->whereNotIn('heading', DemoSliderSeeder::headings())->exists()
            || FeatureItem::query()->whereNotIn('title', DemoFeatureItemSeeder::titles())->exists()
            || PartnerMessage::query()->where(fn ($query) => $query->where('name', '!=', 'Alex Morgan')->orWhere('organization', '!=', 'Buildora Construction & Engineering'))->exists()
            || GalleryImage::query()->exists()
            || SocialLink::query()->exists();

        if ($hasCustomerContent && ! $this->acknowledged('CMS_ALLOW_DEMO_SEED_WITH_EXISTING_DATA')) {
            throw new RuntimeException('Existing customer content detected. Review it and set CMS_ALLOW_DEMO_SEED_WITH_EXISTING_DATA=true to add demo records without deleting data.');
        }

        if ($hasCustomerContent && $this->command) {
            $this->command->warn('Existing customer content was detected and will be left unchanged.');
        }
    }

    private function acknowledged(string $key): bool
    {
        return filter_var(env($key, false), FILTER_VALIDATE_BOOL);
    }
}
