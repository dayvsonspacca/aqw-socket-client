<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Scripts\ClientContext;

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
     * Called once by the client before the event loop begins.
     *
     * Returns the first command to send, or null if no immediate action is needed.
     * Default implementation in AbstractScript returns null.
     *
     * @param ClientContext $context Shared session state.
     */
    public function start(ClientContext $context): ?CommandInterface;

    /**
     * Handles an incoming event.
     *
     * Returns at most one command to send. The client queues it and sends it
     * on the next available tick.
     *
     * @param EventInterface $event The incoming event.
     * @param ClientContext  $context Shared session state.
     *
     * @return ?CommandInterface A command to dispatch, or null.
     */
    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface;

    /**
     * Returns the list of event types this script is interested in.
     *
     * @return array<class-string<EventInterface>>
     */
    public function handles(): array;

    /**
     * Signals whether this script has completed its work.
     *
     * Checked by the client after every {@see AqwSocketClient\Interfaces\ScriptInterface::handle()} call.
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
     */
    public function failed(): void;

    /**
     * Marks the script as disconnected.
     */
    public function disconnected(): void;

    /**
     * Marks the script as successfully completed.
     */
    public function success(): void;
}
