<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

/**
 * Interface responsible for converting raw server messages
 * ({@see AqwSocketClient\Interfaces\MessageInterface}) into one or more executable
 * {@see AqwSocketClient\Interfaces\EventInterface} objects.
 *
 * This component acts as the **parser** for the incoming socket data.
 */
interface InterpreterInterface
{
    /**
     * Parses a raw server message and generates an array of event objects.
     *
     * A single message may contain multiple distinct events.
     *
     * @param MessageInterface $message The raw message received from the socket.
     * @return EventInterface[] An array containing one or more event objects.
     */
    public function interpret(MessageInterface $message): array;
}