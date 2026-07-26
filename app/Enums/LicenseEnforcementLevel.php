<?php

namespace App\Enums;

enum LicenseEnforcementLevel: string
{
    case Informational = 'informational';
    case UpdatesRestricted = 'updates_only';
    case PremiumRestricted = 'premium_restricted';
    case ComplianceWarning = 'compliance_warning';
}
