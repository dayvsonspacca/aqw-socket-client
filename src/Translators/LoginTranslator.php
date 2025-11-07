<?php

declare(strict_types=1);

namespace AqwSocketClient\Translators;

use AqwSocketClient\Commands\FirstLoginCommand;
use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Events\LoginResponseEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\TranslatorInterface;

/**
 * Translator responsible for converting events related to the **initial connection and login**
 * phase into executable commands.
 *
 * It holds the client's credentials to generate the necessary login commands.
 */
class LoginTranslator implements TranslatorInterface
{
    /**
     * @param string $username The username used for the login process.
     * @param string $token The authentication token (password/ticket) used for login.
     */
    public function __construct(
        private readonly string $username,
        private readonly string $token
    ) {}

    /**
     * Translates specific login-related events into commands.
     *
     * - **ConnectionEstabilishedEvent**: Generates a {@see AqwSocketClient\Commands\LoginCommand} using the stored credentials.
     * - **LoginResponseEvent**: Generates a {@see AqwSocketClient\Commands\FirstLoginCommand} only if the login was successful.
     *
     * @param EventInterface $event The incoming event to be translated.
     * @return CommandInterface|false The next command to be sent to the server, or **false**
     * if the event does not require a command response.
     */
    public function translate(EventInterface $event): CommandInterface|false
    {
        return match ($event::class) {
            ConnectionEstabilishedEvent::class => new LoginCommand($this->username, $this->token),
            LoginResponseEvent::class => (fn() => $event->success ? new FirstLoginCommand() : false )(),
            default => false
        };
    }
}