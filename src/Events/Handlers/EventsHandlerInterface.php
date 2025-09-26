<?php

declare(strict_types=1);

namespace AqwSocketClient\Events\Handlers;

use AqwSocketClient\Commands\CommandInterface;

/**
 * Interface defining a handler responsible for processing events.
 *
 * Implementations of this interface should handle one or more events and
 * optionally generate commands to be sent back to the server in response.
 */
interface EventsHandlerInterface
{
    /**
     * Handles an array of events and returns commands to be executed.
     *
     * @param EventInterface[] $events The events to process.
     * @return CommandInterface[] An array of commands generated as a response to the events.
     */
    public function handle(array $events): array;
}
