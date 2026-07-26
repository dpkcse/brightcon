<?php

namespace Database\Seeders;

use Database\Seeders\Demo\DemoContentSeeder;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $mode = strtolower(trim((string) env('CMS_SEED_MODE', 'clean')));

        if (! in_array($mode, ['clean', 'demo'], true)) {
            throw new InvalidArgumentException("Unsupported CMS_SEED_MODE [{$mode}]. Expected clean or demo.");
        }

        $this->call(EssentialSystemSeeder::class);

        if ($mode === 'demo') {
            $this->call(DemoContentSeeder::class);
        }
    }
}
