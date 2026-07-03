<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::query()->doesntExist()) {
            User::query()->create([
                'name' => 'Local Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
