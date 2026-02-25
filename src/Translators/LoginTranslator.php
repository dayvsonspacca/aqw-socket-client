<?php

declare(strict_types=1);

namespace AqwSocketClient\Translators;

use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\TranslatorInterface;
use Override;

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
        #[\SensitiveParameter]
        private readonly string $token,
    ) {}

    /**
     * Translates specific login-related events into commands.
     *
     * - **ConnectionEstablishedEvent**: Generates a {@see LoginCommand} using the stored credentials.
     * - **LoginRespondedEvent**: Generates a {@see JoinInitialAreaCommand} only if the login was successful.
     *
     * @param EventInterface $event The incoming event to be translated.
     * @return CommandInterface|null The next command to be sent to the server, or **null**
     * if the event does not require a command response.
     */
    #[Override]
    public function translate(EventInterface $event): ?CommandInterface
    {
        return match ($event::class) {
            ConnectionEstablishedEvent::class => new LoginCommand($this->username, $this->token),
            LoginRespondedEvent::class => (static fn() => $event->success ? new JoinInitialAreaCommand() : null)(),
            default => null,
        };
    }
}
