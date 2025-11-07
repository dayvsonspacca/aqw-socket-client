<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

/**
 * Interface responsible for converting raw server messages
 * into {@see AqwSocketClient\Interfaces\EventInterface} objects.
 */
interface InterpreterInterface
{
    /**
     * @return EventInterface[]
     */
    public function interpret(MessageInterface $message): array;
}
