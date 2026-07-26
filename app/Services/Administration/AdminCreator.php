<?php

namespace App\Services\Administration;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class AdminCreator
{
    public static function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email:rfc', 'max:255'], 'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()]];
    }

    public function create(array $credentials): User
    {
        $validator = Validator::make($credentials, self::rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        if (User::query()->where('email', $credentials['email'])->exists()) {
            throw new RuntimeException('An account with that email already exists; no changes were made.');
        }

        return User::query()->create(['name' => $credentials['name'], 'email' => $credentials['email'], 'password' => Hash::make($credentials['password']), 'is_admin' => true]);
    }
}
