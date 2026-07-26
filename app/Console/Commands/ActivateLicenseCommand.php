<?php

namespace App\Console\Commands;

use App\Licensing\LicenseManager;
use Illuminate\Console\Command;

class ActivateLicenseCommand extends Command
{
    protected $signature = 'cms:license:activate
        {--provider= : Configured provider identifier}
        {--license-file= : Path to a credential file outside the public web root}
        {--host= : Licensed hostname; defaults to APP_URL}';

    protected $description = 'Activate a product license without exposing its credential in process arguments';

    public function handle(LicenseManager $licenses): int
    {
        $path = (string) $this->option('license-file');
        if ($path === '' || ! is_file($path) || ! is_readable($path)) {
            $this->error('A readable --license-file is required.');

            return self::FAILURE;
        }

        $credential = trim((string) file_get_contents($path));
        if ($credential === '') {
            $this->error('The license file is empty.');

            return self::FAILURE;
        }

        $provider = (string) ($this->option('provider') ?: config('licensing.default_provider'));
        $host = (string) ($this->option('host') ?: parse_url((string) config('app.url'), PHP_URL_HOST));
        $decision = $licenses->activate($provider, $credential, $host);

        if (! $decision->permitsUse()) {
            $this->error('Activation rejected: '.($decision->reason ?: $decision->status->value));

            return self::FAILURE;
        }

        $this->info("License activated with provider [{$provider}].");

        return self::SUCCESS;
    }
}
