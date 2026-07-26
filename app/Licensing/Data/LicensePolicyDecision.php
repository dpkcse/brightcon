<?php

namespace App\Licensing\Data;

use App\Enums\LicenseEnforcementLevel;
use App\Enums\LicenseStatus;

final readonly class LicensePolicyDecision
{
    public function __construct(
        public string $action,
        public bool $allowed,
        public LicenseStatus $status,
        public LicenseEnforcementLevel $level,
        public ?string $notice = null,
        public string $noticePriority = 'information',
    ) {}
}
