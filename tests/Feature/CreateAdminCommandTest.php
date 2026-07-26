<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_interactive_options_create_a_hashed_administrator_without_printing_password(): void
    {
        $credential = 'Strong!Pass123';
        $this->artisan('cms:create-admin', ['--name' => 'Operator', '--email' => 'operator@example.com', '--password' => $credential])
            ->expectsOutputToContain('Administrator created successfully.')
            ->doesntExpectOutputToContain($credential)
            ->assertSuccessful();

        $user = User::firstOrFail();
        $this->assertTrue($user->is_admin);
        $this->assertTrue(Hash::check($credential, $user->password));
    }

    public function test_invalid_email_and_weak_password_are_rejected(): void
    {
        $this->artisan('cms:create-admin', ['--name' => 'Operator', '--email' => 'invalid', '--password' => 'weak'])->assertExitCode(2);
        $this->assertSame(0, User::count());
    }

    public function test_existing_users_are_not_reset_or_silently_promoted(): void
    {
        $user = User::create(['name' => 'Customer', 'email' => 'customer@example.com', 'password' => 'Original!Pass123', 'is_admin' => false]);
        $hash = $user->password;

        $this->artisan('cms:create-admin', ['--name' => 'Changed', '--email' => $user->email, '--password' => 'Different!Pass123'])->assertFailed();
        $user->refresh();
        $this->assertFalse($user->is_admin);
        $this->assertSame($hash, $user->password);

        $this->artisan('cms:create-admin', ['--name' => 'Changed', '--email' => $user->email, '--password' => 'Different!Pass123', '--promote-existing' => true])->assertSuccessful();
        $user->refresh();
        $this->assertTrue($user->is_admin);
        $this->assertSame('Customer', $user->name);
        $this->assertSame($hash, $user->password);
    }
}
