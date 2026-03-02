<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

use AqwSocketClient\Enums\ScriptResult;

/**
 * Represents a single unit of logic to be executed against a {@see AqwSocketClient\Interfaces\ClientInterface}.
 *
 * Scripts are composable — a script can be a single atomic step or a
 * sequence of other scripts. The client drives the execution loop,
 * advancing to the next script only when the current one is done.
 */
interface ScriptInterface
{
    /**
     * Handles an incoming event.
     *
     * Implementations may inspect the event and return zero or more
     * commands to be sent back to the server.
     *
     * @param EventInterface $event The incoming event.
     *
     * @return CommandInterface[] Commands to be dispatched.
     */
    public function handle(EventInterface $event): array;

    /**
     * Returns the list of event types this script is interested in.
     *
     * @return array<class-string<EventInterface>>
     */
    public function handles(): array;

    /**
     * Signals whether this script has completed its work.
     *
     * Checked by the client after every {@see AqwSocketClient\Interfaces\ScriptInterface::handle()()} call.
     * When true, the client stops driving this script and moves on.
     */
    public function isDone(): bool;

    /**
     * Returns the final execution result of the script.
     *
     * Should only be relied upon once {@see AqwSocketClient\Interfaces\ScriptInterface::isDone()} returns true.
     */
    public function result(): ScriptResult;

    /**
     * Marks the script as failed.
     *
     * Sets the result to {@see AqwSocketClient\Enums\ScriptResult::Failed}
     * and completes execution.
     */
    public function failed(): void;

    /**
     * Marks the script as disconnected.
     *
     * Sets the result to {@see AqwSocketClient\Enums\ScriptResult::Disconnected}
     * and completes execution.
     */
    public function disconnected(): void;

    /**
     * Marks the script as successfully completed.
     *
     * Sets the result to {@see AqwSocketClient\Enums\ScriptResult::Success}
     * and completes execution.
     */
    public function success(): void;
}
