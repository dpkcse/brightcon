<?php

namespace App\Console\Commands;

use App\Enums\InstallationState;
use App\Models\SiteSetting;
use App\Services\Installation\InstallationStateService;
use Illuminate\Console\Command;

class AdoptExistingInstallationCommand extends Command
{
    protected $signature = 'cms:adopt-existing-installation {--confirm : Explicitly reconcile a verified legacy installation}';

    protected $description = 'Preview or safely adopt a demonstrably legacy-installed website';

    public function handle(InstallationStateService $state): int
    {
        $this->call('cms:installation-status');
        if ($state->state() !== InstallationState::LegacyInstalled) {
            $this->error('The application is not demonstrably legacy-installed; no changes were made.');

            return self::FAILURE;
        } if (! $this->option('confirm')) {
            $this->warn('Preview only. Re-run with --confirm after backup and review.');

            return self::SUCCESS;
        } if ($this->input->isInteractive() && ! $this->confirm('Write only completion fields and the installation marker?')) {
            return self::FAILURE;
        } $settings = SiteSetting::query()->firstOrFail();
        $settings->forceFill(['installation_completed_at' => $settings->installation_completed_at ?: now(), 'installed_version' => $settings->installed_version ?: config('cms.product.version')])->save();
        $state->markInstalled();
        $this->info('Legacy installation adopted. No users, passwords, content, migrations, or seed data were changed.');

        return self::SUCCESS;
    }
}
