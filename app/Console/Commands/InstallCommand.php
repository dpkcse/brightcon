<?php

namespace App\Console\Commands;

use App\Services\Installation\InstallationManager;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'cms:install {--app-name=} {--app-url=} {--db-host=127.0.0.1} {--db-port=3306} {--db-name=} {--db-user=} {--db-password=} {--admin-name=} {--admin-email=} {--admin-password=} {--seed=clean} {--no-storage-link} {--force-non-interactive-confirmation} {--approve-env-update}';

    protected $description = 'Securely install Buildora CMS using the shared installation pipeline';

    public function handle(InstallationManager $manager): int
    {
        $interactive = $this->input->isInteractive();
        if (($this->option('db-password') || $this->option('admin-password')) && $interactive) {
            $this->warn('Command-line passwords can appear in shell history and process lists; prefer hidden prompts.');
        } $get = fn ($option, $question, $secret = false) => $this->option($option) ?: ($interactive ? ($secret ? $this->secret($question) : $this->ask($question)) : null);
        $databasePassword = $this->option('db-password') ?? ($interactive ? $this->secret('Database password (leave empty for none)') : '');
        $data = ['app_name' => $get('app-name', 'Application name'), 'app_url' => $get('app-url', 'Application URL'), 'db_host' => $get('db-host', 'Database host'), 'db_port' => $get('db-port', 'Database port'), 'db_name' => $get('db-name', 'Database name'), 'db_user' => $get('db-user', 'Database username'), 'db_password' => $databasePassword ?? '', 'admin_name' => $get('admin-name', 'Administrator name'), 'admin_email' => $get('admin-email', 'Administrator email'), 'admin_password' => $get('admin-password', 'Administrator password', true), 'seed' => $this->option('seed'), 'storage_link' => ! $this->option('no-storage-link')];
        if (! in_array($data['seed'], ['clean', 'demo'], true)) {
            $this->error('Seed mode must be clean or demo.');

            return self::INVALID;
        } if (collect($data)->except(['storage_link', 'db_password'])->contains(fn ($v) => $v === null || $v === '')) {
            $this->error('All installation values are required in non-interactive mode.');

            return self::INVALID;
        } if (! $interactive && ! $this->option('force-non-interactive-confirmation')) {
            $this->error('Non-interactive installation requires --force-non-interactive-confirmation.');

            return self::FAILURE;
        } if ($interactive && ! $this->confirm('Install without deleting or resetting existing data?')) {
            return self::FAILURE;
        } try {
            $result = $manager->install($data, (bool) $this->option('approve-env-update'));
            $this->info('Buildora CMS installation completed for '.$result['admin_email'].'.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
