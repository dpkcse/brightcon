<?php

namespace App\Contracts;

use App\Enums\InstallationState;

interface InstallationStateInterface
{
    public function isInstalled(): bool;

    public function state(): InstallationState;

    public function canRunInstaller(): bool;

    public function markInstalled(): void;

    public function diagnostics(): array;
}
