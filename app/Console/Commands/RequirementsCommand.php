<?php

namespace App\Console\Commands;

use App\Services\Installation\RequirementChecker;
use Illuminate\Console\Command;

class RequirementsCommand extends Command
{
    protected $signature = 'cms:requirements';

    protected $description = 'Check Buildora CMS server requirements without exposing sensitive server data';

    public function handle(RequirementChecker $checker): int
    {
        foreach ($checker->check() as $item) {
            $this->line(($item['passed'] ? 'PASS' : 'FAIL').' '.$item['name'].' — '.$item['detected'].($item['blocking'] ? '' : ' (recommended)'));
        }

return $checker->passes() ? self::SUCCESS : self::FAILURE;
    }
}
