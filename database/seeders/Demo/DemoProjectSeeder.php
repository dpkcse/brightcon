<?php

namespace Database\Seeders\Demo;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoProjectSeeder extends Seeder
{
    public static function categorySlugs(): array
    {
        return array_map(fn (string $name): string => 'buildora-demo-'.Str::slug($name), array_keys(self::records()));
    }

    public static function projectSlugs(): array
    {
        return collect(self::records())->flatten(1)->map(fn (array $project): string => 'buildora-demo-'.Str::slug($project[0]))->all();
    }

    public function run(): void
    {
        DB::transaction(function (): void {
            foreach (self::records() as $categoryName => $projects) {
                $category = ProjectCategory::query()->firstOrCreate(
                    ['slug' => 'buildora-demo-'.Str::slug($categoryName)],
                    ['name' => $categoryName, 'description' => "Fictional {$categoryName} project demonstrations.", 'status' => true, 'sort_order' => array_search($categoryName, array_keys(self::records()), true) + 1],
                );

                // A buyer may already own even our reserved slug. Never attach demo
                // projects to that record unless it has the expected demo identity.
                if ($category->name !== $categoryName) {
                    continue;
                }

                foreach ($projects as $project) {
                    $description = $project[1];
                    Project::query()->firstOrCreate(
                        ['slug' => 'buildora-demo-'.Str::slug($project[0])],
                        ['project_category_id' => $category->id, 'title' => $project[0], 'progress_percentage' => $project[2], 'short_description' => $description, 'full_description' => $project[3], 'status' => true, 'is_featured' => true, 'sort_order' => $project[4], 'seo_title' => $project[0], 'seo_description' => $description],
                    );
                }
            }
        });
    }

    private static function records(): array
    {
        return [
            'Commercial' => [['Metroview Corporate Office Complex', 'A fictional multi-storey workplace planned for flexible teams and efficient circulation.', 100, 'The demonstration record presents coordinated structural, envelope, services, and workplace delivery without identifying a real client or contract.', 1]],
            'Industrial' => [['Northgate Industrial Production Facility', 'A fictional production facility organized around safe material and workforce movement.', 82, 'The demo scope coordinates production areas, utilities, logistics routes, and phased commissioning for a generic industrial operation.', 2]],
            'Residential' => [['Riverstone Residential Development', 'A fictional residential community balancing shared amenities and practical homes.', 68, 'This demonstration project highlights repeatable building systems, landscape interfaces, access planning, and quality-controlled handover.', 3]],
            'Infrastructure' => [['Eastline Drainage and Road Improvement', 'A fictional corridor improvement combining drainage renewal and safer road access.', 55, 'The demonstration describes staged civil works, traffic coordination, drainage improvements, and surface reinstatement without real location claims.', 4]],
            'Interior and Fit-Out' => [['Meridian Commercial Interior Fit-Out', 'A fictional commercial interior shaped for adaptable work and visitor areas.', 100, 'The demo fit-out coordinates layouts, finishes, lighting, services, joinery, and final inspections through a structured delivery programme.', 5]],
            'Rehabilitation' => [['Summit Structural Rehabilitation Project', 'A fictional structural renewal programme based on assessment and planned intervention.', 74, 'The demonstration scope presents controlled repairs, strengthening, monitoring, and phased access while making no claims about a real structure.', 6]],
        ];
    }
}
