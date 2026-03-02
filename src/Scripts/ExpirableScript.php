<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Interfaces\ExpirableScriptInterface;
use DateTimeImmutable;
use Override;

/**
 * Base implementation for atomic expirable scripts.
 *
 * Extends {@see AqwSocketClient\Scripts\AbstractScript} by adding
 * time-based expiration support.
 */
abstract class ExpirableScript extends AbstractScript implements ExpirableScriptInterface
{
    private ?DateTimeImmutable $expiresAt = null;

    #[Override]
    public function expired(): void
    {
        $this->done();
        $this->result = ScriptResult::Expired;
    }

    #[Override]
    public function expiresAt(DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    #[Override]
    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            $this->expiresAt(new DateTimeImmutable('+1 minute'));
            return false;
        }

        return new DateTimeImmutable() > $this->expiresAt;
    }
}
