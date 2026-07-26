<?php

namespace Database\Seeders\Demo;

use App\Models\PartnerMessage;
use Illuminate\Database\Seeder;

class DemoPartnerMessageSeeder extends Seeder
{
    public function run(): void
    {
        PartnerMessage::query()->firstOrCreate(
            ['name' => 'Alex Morgan', 'organization' => 'Buildora Construction & Engineering'],
            ['designation' => 'Managing Director', 'image_path' => null, 'organization_logo_path' => null, 'short_message' => 'A fictional message illustrating how leadership content appears in Buildora CMS.', 'full_message' => 'This fictional demonstration message describes a commitment to clear planning, responsible engineering, safe delivery, and constructive collaboration. It does not represent a real person, company, or endorsement.', 'highlighted_text' => 'Fictional demonstration content', 'linkedin_url' => null, 'display_order' => 1, 'is_featured' => true, 'is_active' => true, 'published_at' => now()],
        );
    }
}
