<?php

namespace App\Console\Commands;

use App\Services\Installation\InstallationStateService;
use Illuminate\Console\Command;

class InstallationStatusCommand extends Command
{
    protected $signature = 'cms:installation-status {--json}';

    protected $description = 'Show non-secret layered installation diagnostics';

    public function handle(InstallationStateService $state): int
    {
        $data = $state->diagnostics();
        $this->option('json') ? $this->line(json_encode($data, JSON_PRETTY_PRINT)) : $this->table(['Signal', 'Value'], collect($data)->map(fn ($v, $k) => [$k, is_bool($v) ? ($v ? 'yes' : 'no') : $v])->all());

        return $state->state()->value === 'inconsistent' ? self::FAILURE : self::SUCCESS;
    }
}
