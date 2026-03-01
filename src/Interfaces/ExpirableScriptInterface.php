<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

use DateTimeImmutable;

/**
 * Represents an {@see AqwSocketClient\Interfaces\ScriptInterface} with a lifetime.
 */
interface ExpirableScriptInterface extends ScriptInterface
{
    public function isExpired(): bool;

    public function expiresAt(DateTimeImmutable $expiresAt): void;
}
