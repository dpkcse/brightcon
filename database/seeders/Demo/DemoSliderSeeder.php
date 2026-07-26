<?php

namespace Database\Seeders\Demo;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class DemoSliderSeeder extends Seeder
{
    public static function headings(): array
    {
        return ['Engineering Excellence, Delivered', 'Building Infrastructure for Tomorrow', 'From Concept to Completion'];
    }

    public function run(): void
    {
        foreach (self::headings() as $index => $heading) {
            Slider::query()->firstOrCreate(
                ['heading' => $heading],
                ['sub_heading' => 'Fictional Buildora demonstration', 'description' => 'Explore a text-first demonstration of coordinated construction and engineering content.', 'image' => null, 'button_text' => $index === 2 ? 'Contact Us' : 'Explore Projects', 'button_link' => $index === 2 ? '/contact' : '/projects', 'sort_order' => $index + 1, 'status' => true],
            );
        }
    }
}
