<?php

namespace App\Console\Commands;

use App\Licensing\LicenseManager;
use Illuminate\Console\Command;

class LicenseStatusCommand extends Command
{
    protected $signature = 'cms:license:status';

    protected $description = 'Display the local product license status';

    public function handle(LicenseManager $licenses): int
    {
        $activation = $licenses->current();
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
