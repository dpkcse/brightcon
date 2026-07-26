<?php

namespace App\Licensing\Data;

final readonly class ActivationRequest
{
    public function __construct(
        public string $credential,
        public string $installationId,
        public string $host,
        public string $product,
    ) {}
}
