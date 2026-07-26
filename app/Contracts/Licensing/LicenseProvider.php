<?php

namespace App\Contracts\Licensing;

use App\Licensing\Data\ActivationRequest;
use App\Licensing\Data\LicenseDecision;

interface LicenseProvider
{
    public function id(): string;

    public function capabilities(): ProviderCapabilities;

    public function activate(ActivationRequest $request): LicenseDecision;
}
