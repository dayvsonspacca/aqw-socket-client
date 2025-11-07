<?php

declare(strict_types=1);

namespace AqwSocketClient;

use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\ListenerInterface;
use AqwSocketClient\Interfaces\TranslatorInterface;
use AqwSocketClient\Interpreters\LoginInterpreter;
use AqwSocketClient\Translators\LoginTranslator;

/**
 * Encapsulates all necessary configuration for the {@see AqwSocketClient\Client}, including
 * user credentials and the collection of pipeline components.
 *
 * This class ensures essential components, like the {@see AqwSocketClient\Interpreters\LoginInterpreter} and
 * {@see LoginTranslator}, are registered by default.
 */
class Configuration
{
    /**
     * @var InterpreterInterface[] A collection of message interpreters responsible for
     * converting raw messages into {@see AqwSocketClient\Interfaces\EventInterface} objects.
     */
    public readonly array $interpreters;

    /**
     * @var TranslatorInterface[] A collection of event translators responsible for
     * converting {@see EventInterface} objects into {@see AqwSocketClient\Interfaces\CommandInterface} objects.
     */
    public readonly array $translators;

    /**
     * @var ListenerInterface[] A collection of listeners that execute application logic
     * based on received {@see AqwSocketClient\Interfaces\EventInterface} objects.
     */
    public readonly array $listeners;

    /**
     * @param string $username The client's username for authentication.
     * @param string $password The client's password (often not used directly for socket login, but stored).
     * @param string $token The client's authentication token (or ticket) used for socket login.
     * @param array $interpreters Additional custom interpreters to be registered.
     * @param array $translators Additional custom translators to be registered.
     * @param array $listeners Custom listeners to be registered.
     * @param bool $logMessages If **true**, all raw incoming server messages will be logged to the console.
     */
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly string $token,
        array $interpreters = [],
        array $translators = [],
        array $listeners = [],
        public readonly bool $logMessages = false
    ) {
        $this->interpreters = array_merge([new LoginInterpreter()], $interpreters);
        $this->translators = array_merge([new LoginTranslator($username, $token)], $translators);
        $this->listeners = $listeners;
    }
}