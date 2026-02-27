<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

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
     * Reacts to an incoming event, returning any commands to be sent.
     *
     * @return CommandInterface[]
     */
    public function handle(EventInterface $event): array;

    /**
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
}
