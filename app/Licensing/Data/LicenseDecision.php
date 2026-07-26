<?php

namespace App\Licensing\Data;

use App\Enums\LicenseStatus;
use DateTimeImmutable;

final readonly class LicenseDecision
{
    public function __construct(
        public LicenseStatus $status,
        public ?string $externalReference = null,
        public ?DateTimeImmutable $expiresAt = null,
        public array $metadata = [],
        public ?string $reason = null,
    ) {}

    public function permitsUse(): bool
    {
        return $this->status === LicenseStatus::Active
            && ($this->expiresAt === null || $this->expiresAt > new DateTimeImmutable);
    }
}
