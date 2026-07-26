<?php

namespace Database\Seeders\Demo;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class DemoOrganizationSeeder extends Seeder
{
    public static function names(): array
    {
        return ['Apex Holdings', 'Northbridge Industries', 'UrbanEdge Developers', 'Summit Infrastructure', 'Meridian Properties', 'Vertex Engineering Group'];
    }

    public function run(): void
    {
        foreach (self::names() as $index => $name) {
            Organization::query()->firstOrCreate(
                ['name' => $name],
                ['logo' => null, 'website_url' => null, 'is_active' => true, 'is_featured' => $index < 4, 'display_order' => $index + 1],
            );
        }
    }
}
