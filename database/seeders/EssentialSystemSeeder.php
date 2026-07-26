<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EssentialSystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DefaultSettingsSeeder::class,
            HomepageSectionSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
