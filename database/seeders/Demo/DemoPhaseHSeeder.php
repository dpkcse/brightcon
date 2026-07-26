<?php

namespace Database\Seeders\Demo;

use App\Models\Competency;
use App\Models\Equipment;
use App\Models\Page;
use Illuminate\Database\Seeder;

class DemoPhaseHSeeder extends Seeder
{
    public const PAGE_SLUGS = ['demo-hse-policy', 'demo-quality-policy', 'demo-sustainability-commitment'];

    public const EQUIPMENT_SLUGS = ['demo-tower-crane', 'demo-concrete-mixer', 'demo-excavator', 'demo-mobile-crane', 'demo-scaffolding-system', 'demo-surveying-equipment'];

    public const COMPETENCY_SLUGS = ['demo-structural-engineering', 'demo-infrastructure-works', 'demo-quality-assurance', 'demo-health-safety', 'demo-project-planning', 'demo-construction-management'];

    public function run(): void
    {
        foreach (array_combine(self::PAGE_SLUGS, ['Health, Safety and Environment', 'Quality Policy', 'Sustainability Commitment']) as $slug => $title) {
            Page::firstOrCreate(['slug' => $slug], ['title' => $title, 'excerpt' => 'Fictional demonstration content for CMS evaluation.', 'content' => '<p>This is fictional demonstration policy content. Replace it before publication.</p>', 'status' => 'draft']);
        }

        foreach (array_combine(self::EQUIPMENT_SLUGS, ['Tower Crane', 'Concrete Mixer', 'Excavator', 'Mobile Crane', 'Scaffolding System', 'Surveying Equipment']) as $slug => $name) {
            Equipment::firstOrCreate(['slug' => $slug], ['name' => $name, 'short_description' => 'Fictional demonstration capability; no ownership claim is made.', 'status' => 'draft']);
        }

        foreach (array_combine(self::COMPETENCY_SLUGS, ['Structural Engineering', 'Infrastructure Works', 'Quality Assurance', 'Health and Safety', 'Project Planning', 'Construction Management']) as $slug => $title) {
            Competency::firstOrCreate(['slug' => $slug], ['title' => $title, 'short_description' => 'Fictional demonstration competency content.', 'status' => 'draft']);
        }
    }
}
