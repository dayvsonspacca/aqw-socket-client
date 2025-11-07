<?php

declare(strict_types=1);

namespace AqwSocketClient\Translators;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\TranslatorInterface;

class LoginTranslator implements TranslatorInterface
{
    public function __construct(
        private readonly string $username,
        private readonly string $token
    ) {}

    public function translate(EventInterface $event): CommandInterface|false
    {
        return match ($event::class) {
            ConnectionEstabilishedEvent::class => new LoginCommand($this->username, $this->token),
            default => false
        };
    }
}
