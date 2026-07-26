<?php

namespace App\Contracts\Licensing;

final readonly class ProviderCapabilities
{
    public function __construct(
        public bool $activation,
        public bool $deactivation,
        public bool $remoteValidation,
        public bool $offlineActivation,
    ) {}
}
