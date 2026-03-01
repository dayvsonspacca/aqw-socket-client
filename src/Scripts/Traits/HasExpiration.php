<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts\Traits;

use DateTimeImmutable;

trait HasExpiration
{
    private ?DateTimeImmutable $expiresAt = null;

    public function expiresAt(DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return new DateTimeImmutable() > $this->expiresAt;
    }
}
