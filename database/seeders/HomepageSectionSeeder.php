<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class HomepageSectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->sections() as $section) {
            HomepageSection::query()->firstOrCreate(
                ['section_key' => $section['section_key']],
                $section,
            );
        }
    }

    private function sections(): array
    {
        return [
            ['section_key' => 'about', 'title' => 'About', 'subtitle' => null, 'sort_order' => 10, 'status' => true],
            ['section_key' => 'partner_messages', 'title' => 'Leadership Messages', 'subtitle' => null, 'sort_order' => 20, 'status' => true],
            ['section_key' => 'project_highlights', 'title' => 'Project Highlights', 'subtitle' => null, 'sort_order' => 30, 'status' => true],
            ['section_key' => 'gallery_cta', 'title' => 'Gallery', 'subtitle' => null, 'sort_order' => 40, 'status' => true],
            ['section_key' => 'services', 'title' => 'Services', 'subtitle' => null, 'sort_order' => 50, 'status' => true],
        ];
    }
}
