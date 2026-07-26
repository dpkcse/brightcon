<?php

namespace Database\Seeders\Demo;

use App\Models\FeatureItem;
use Illuminate\Database\Seeder;

class DemoFeatureItemSeeder extends Seeder
{
    public static function titles(): array
    {
        return ['Quality Engineering', 'Safety-Focused Delivery', 'Reliable Project Management', 'Sustainable Construction'];
    }

    public function run(): void
    {
        $texts = [
            'Disciplined reviews connect design intent with practical delivery.',
            'Planning and field coordination place safe work at the centre of delivery.',
            'Clear scopes, programmes, and reporting support dependable decisions.',
            'Thoughtful material and resource choices support responsible outcomes.',
        ];

        foreach (self::titles() as $index => $title) {
            FeatureItem::query()->firstOrCreate(
                ['title' => $title],
                ['short_text' => $texts[$index], 'icon_class' => 'fa-solid fa-check', 'image' => null, 'sort_order' => $index + 1, 'status' => true],
            );
        }
    }
}
