<?php

declare(strict_types=1);

namespace AqwSocketClient\Factories;

use AqwSocketClient\Commands\{AfterLoginCommand, LoginCommand};
use AqwSocketClient\Events\{ConnectionEstabilishedEvent, EventInterface, EventsHandlerInterface, LoginSuccessfulEvent, RawMessageEvent};

/**
 * Handles core events received from the AQW server and generates appropriate commands.
 *
 * This handler reacts to specific events such as connection established and login successful,
 * generating commands like {@see AqwSocketClient\Commands\LoginCommand} and {@see AqwSocketClient\Commands\AfterLoginCommand} accordingly.
 */
class CoreEventsHandler implements EventsHandlerInterface
{
    /**
     * CoreEventsHandler constructor.
     *
     * @param string $playerName The name of the player to login.
     * @param string $token The authentication token used for login.
     */
    public function __construct(
        private readonly string $playerName,
        private readonly string $token
    ) {
    }

    /**
     * Handles an array of events and returns commands to be sent to the server.
     *
     * @param EventInterface[] $events The events to handle.
     * @return array An array of command objects generated based on the events.
     */
    public function handle(array $events): array
    {
        $commands = [];
        foreach ($events as $event) {
            $eventName = get_class($event);

            $commands = array_merge($commands, match ($eventName) {
                ConnectionEstabilishedEvent::class => [
                    new LoginCommand($this->playerName, $this->token)
                ],
                LoginSuccessfulEvent::class => [
                    new AfterLoginCommand()
                ],
                RawMessageEvent::class => [],
                default => []
            });
        }

        return $commands;
    }
}
