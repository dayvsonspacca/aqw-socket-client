<?php

declare(strict_types=1);

namespace AqwSocketClient;

use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\ListenerInterface;
use AqwSocketClient\Interfaces\TranslatorInterface;

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

    /**
     * @var TranslatorInterface[]
     */
    public array $translators = [];

    /**
     * @var ListenerInterface[]
     */
    public array $listeners = [];

    private function __construct(
        public readonly Server $server,
    ) {}

    public static function make(Server $server): self
    {
        return new self($server);
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

    /**
     * Registers an event translator responsible for converting
     * {@see AqwSocketClient\Interfaces\EventInterface} objects into {@see AqwSocketClient\Interfaces\CommandInterface} objects.
     */
    public function addTranslator(TranslatorInterface $translator): self
    {
        $this->translators[] = $translator;
        return $this;
    }

    /**
     * Registers a listener that executes application logic
     * based on received {@see AqwSocketClient\Interfaces\EventInterface} objects.
     */
    public function addListener(ListenerInterface $listener): self
    {
        $this->listeners[] = $listener;
        return $this;
    }
}
