<?php

namespace App\Console\Commands;

use App\Licensing\LicenseManager;
use Illuminate\Console\Command;
use Throwable;

class LicenseStatusCommand extends Command
{
    protected $signature = 'cms:license:status';

    protected $description = 'Display the local product license status';

    public function handle(LicenseManager $licenses): int
    {
        try {
            $activation = $licenses->current();
        } catch (Throwable) {
            $this->warn('License status is unavailable because local storage could not be read. No license secret was displayed.');

            return self::FAILURE;
        }
        if ($activation === null) {
            $this->warn('No license activation is stored.');

            return self::FAILURE;
        }

        $this->table(['Provider', 'Status', 'Verified', 'Expires'], [[
            $activation->provider,
            $activation->status->value,
            $activation->verified_at?->toIso8601String() ?? 'never',
            $activation->expires_at?->toIso8601String() ?? 'never',
        ]]);

        return $licenses->permitsUse() ? self::SUCCESS : self::FAILURE;
    }
}
