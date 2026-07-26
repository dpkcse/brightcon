<?php

namespace App\Console\Commands;

use App\Services\Release\ReleaseScanner;
use Illuminate\Console\Command;

class ReleaseAuditCommand extends Command
{
    protected $signature = 'cms:release-audit {path : Extracted or staged package directory}';

    protected $description = 'Scan a release tree for blocking commercial-distribution risks';

    public function handle(ReleaseScanner $scanner): int
    {
        $path = realpath((string) $this->argument('path'));
        if ($path === false || ! is_dir($path)) {
            $this->error('Release tree does not exist.');

            return self::FAILURE;
        }
        $findings = $scanner->scan($path);
        foreach ($findings as $finding) {
            $location = $finding['path'].($finding['line'] ? ':'.$finding['line'] : '');
            $this->line(strtoupper($finding['severity'])." [{$finding['rule']}] {$location} {$finding['context']}");
        }
        $this->newLine();
        $findings === [] ? $this->info('Release audit passed.') : $this->error(count($findings).' blocking finding(s).');

        return $findings === [] ? self::SUCCESS : self::FAILURE;
    }
}
