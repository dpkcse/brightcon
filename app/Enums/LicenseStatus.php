<?php

namespace App\Enums;

enum LicenseStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Expired = 'expired';
    case Revoked = 'revoked';
    case Invalid = 'invalid';
    case DomainMismatch = 'domain_mismatch';
    case WrongProduct = 'wrong_product';
    case ConfigurationMissing = 'configuration_missing';
    case AdapterUnavailable = 'adapter_unavailable';
    case ProviderUnavailable = 'provider_unavailable';
    case RecoveryRequired = 'recovery_required';
}
