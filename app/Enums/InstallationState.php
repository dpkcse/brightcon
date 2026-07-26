<?php

namespace App\Enums;

enum InstallationState: string
{
    case Uninstalled = 'uninstalled';
    case EnvironmentReady = 'environment_ready';
    case MigrationsPending = 'migrations_pending';
    case PartiallyInstalled = 'partially_installed';
    case Installed = 'installed';
    case LegacyInstalled = 'legacy_installed';
    case Inconsistent = 'inconsistent';
}
