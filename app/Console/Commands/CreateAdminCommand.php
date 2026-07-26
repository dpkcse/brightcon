<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Administration\AdminCreator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'cms:create-admin
        {--name= : Administrator name}
        {--email= : Administrator email address}
        {--password= : Administrator password (prefer the secure interactive prompt)}
        {--promote-existing : Explicitly promote an existing non-administrator without changing their password}';

    protected $description = 'Create a CMS administrator using explicit, validated credentials';

    public function handle(AdminCreator $creator): int
    {
        $interactive = $this->input->isInteractive();
        $name = $this->option('name') ?: ($interactive ? $this->ask('Administrator name') : null);
        $email = $this->option('email') ?: ($interactive ? $this->ask('Administrator email') : null);
        $password = $this->option('password') ?: ($interactive ? $this->secret('Password (12+ characters with upper/lowercase, number, and symbol)') : null);

        if ($interactive && ! $this->option('password')) {
            $confirmation = $this->secret('Confirm password');
            if (! hash_equals((string) $password, (string) $confirmation)) {
                $this->error('Password confirmation does not match.');

                return self::FAILURE;
            }
        }

        $validator = Validator::make(compact('name', 'email', 'password'), AdminCreator::rules());

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::INVALID;
        }

        $existing = User::query()->where('email', $email)->first();
        if ($existing) {
            if ($existing->is_admin) {
                $this->warn('An administrator with that email already exists; no changes were made.');

                return self::SUCCESS;
            }

            if (! $this->option('promote-existing')) {
                $this->error('That email belongs to a non-administrator. Use --promote-existing to explicitly promote it without changing its password.');

                return self::FAILURE;
            }

            $existing->update(['is_admin' => true]);
            $this->info('The existing user was promoted. Their name and password were not changed.');

            return self::SUCCESS;
        }

        $creator->create(compact('name', 'email', 'password'));

        $this->info('Administrator created successfully.');

        return self::SUCCESS;
    }
}
