<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $credentials = [
            'name' => env('CMS_ADMIN_NAME'),
            'email' => env('CMS_ADMIN_EMAIL'),
            'password' => env('CMS_ADMIN_PASSWORD'),
        ];

        if (collect($credentials)->every(fn ($value) => blank($value))) {
            return;
        }

        if (collect($credentials)->contains(fn ($value) => blank($value))) {
            throw new InvalidArgumentException('CMS_ADMIN_NAME, CMS_ADMIN_EMAIL, and CMS_ADMIN_PASSWORD must all be supplied.');
        }

        $validator = Validator::make($credentials, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        if (User::query()->where('email', $credentials['email'])->exists()) {
            return;
        }

        User::query()->create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
            'is_admin' => true,
        ]);
    }
}
