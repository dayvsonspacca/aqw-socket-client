<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

/**
 * Interface responsible for converting an incoming {@see AqwSocketClient\Interfaces\EventInterface}
 * (a server message that has been interpreted) into an outgoing
 * {@see AqwSocketClient\Interfaces\CommandInterface} that can be sent back to the server.
 *
 * This process allows the application to **respond** to server events.
 */
interface TranslatorInterface
{
    /**
     * Translates a specific event into a command object that can be
     * processed and sent to the AQW server.
     *
     * @param EventInterface $event The incoming event to be translated.
     * @return CommandInterface|false The command object ready to be sent, or **false**
     * if the event does not require a response/command.
     */
    public function translate(EventInterface $event): CommandInterface|false;
}