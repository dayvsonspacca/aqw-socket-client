<?php

declare(strict_types=1);

namespace AqwSocketClient;

use AqwSocketClient\Interfaces\InterpreterInterface;

/**
 * Encapsulates all necessary configuration for the {@see AqwSocketClient\Interfaces\ClientInterface}, including
 * server settings and the collection of pipeline components.
 */
final class Configuration
{
    /**
     * @var InterpreterInterface[]
     */
    public array $interpreters = [];

    public static function make(): self
    {
        return new self();
    }

    /**
     * Registers a message interpreter responsible for converting raw messages
     * into {@see AqwSocketClient\Interfaces\EventInterface} objects.
     */
    public function addInterpreter(InterpreterInterface $interpreter): self
    {
        $this->interpreters[] = $interpreter;
        return $this;
    }
}
