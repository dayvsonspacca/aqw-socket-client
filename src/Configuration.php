<?php

declare(strict_types=1);

namespace AqwSocketClient;

use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\ListenerInterface;
use AqwSocketClient\Interfaces\TranslatorInterface;
use AqwSocketClient\Interpreters\LoginInterpreter;
use AqwSocketClient\Translators\LoginTranslator;

class Configuration
{
    /** @var InterpreterInterface[] $interpreters */
    public readonly array $interpreters;

    /** @var TranslatorInterface[] $translators */
    public readonly array $translators;

    /** @var ListenerInterface[] $listeners */
    public readonly array $listeners;

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
