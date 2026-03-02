<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

use DateTimeImmutable;

/**
 * Represents an {@see AqwSocketClient\Interfaces\ScriptInterface} with a lifetime.
 */
interface ExpirableScriptInterface extends ScriptInterface
{
    /**
     * Marks this script as expired.
     *
     * Sets the result to {@see AqwSocketClient\Enums\ScriptResult::Expired}
     * and marks the script as completed.
     */
    public function expired(): void;

    /**
     * Determines whether the script has reached its expiration time.
     *
     * @return bool True if the current time is greater than the configured
     *              expiration timestamp; false otherwise.
     */
    public function isExpired(): bool;

    /**
     * Defines the expiration timestamp for this script.
     *
     * @param DateTimeImmutable $expiresAt Absolute expiration time.
     */
    public function expiresAt(DateTimeImmutable $expiresAt): void;
}
