<?php

namespace App\Enums;

enum LicenseStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Expired = 'expired';
    case Revoked = 'revoked';
    case Invalid = 'invalid';
}
