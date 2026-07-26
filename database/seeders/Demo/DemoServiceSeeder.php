<?php

namespace Database\Seeders\Demo;

use App\Models\Service;
use Illuminate\Database\Seeder;

class DemoServiceSeeder extends Seeder
{
    public static function slugs(): array
    {
        return array_column(self::records(), 'slug');
    }

    public function run(): void
    {
        foreach (self::records() as $service) {
            Service::query()->firstOrCreate(['slug' => $service['slug']], $service);
        }
    }

    private static function records(): array
    {
        $items = [
            ['Building and Structural Construction', 'Coordinated structural and building delivery for practical, durable spaces.', 'We coordinate foundations, structural systems, building envelopes, and finishing work through a safety-focused delivery plan.'],
            ['Civil and Infrastructure Works', 'Site, road, drainage, and utility works planned around reliable public use.', 'Our fictional demo team plans civil works with clear sequencing, constructability reviews, and careful coordination of interfaces.'],
            ['Industrial Construction', 'Purpose-built industrial spaces shaped around operational requirements.', 'Industrial delivery combines structural coordination, service integration, logistics planning, and controlled handover activities.'],
            ['Interior and Fit-Out Works', 'Functional interior environments delivered from layout through completion.', 'We demonstrate how the CMS presents coordinated partitions, finishes, building services, fixtures, and final quality reviews.'],
            ['Renovation and Structural Retrofitting', 'Measured upgrades that extend the usefulness of existing structures.', 'Assessment-led renovation plans coordinate repairs and strengthening while considering access, phasing, and ongoing operations.'],
            ['Project Management and Engineering Consultancy', 'Structured planning and technical coordination across each project stage.', 'Consultancy services demonstrate scope definition, programme coordination, design review, reporting, and handover support.'],
        ];

        return array_map(function (array $item, int $index): array {
            $slug = 'buildora-demo-'.str($item[0])->slug();

            return ['title' => $item[0], 'slug' => $slug, 'short_description' => $item[1], 'full_description' => $item[2], 'icon_class' => 'fa-solid fa-helmet-safety', 'status' => true, 'is_featured' => $index < 4, 'sort_order' => $index + 1, 'seo_title' => $item[0], 'seo_description' => $item[1]];
        }, $items, array_keys($items));
    }
}
