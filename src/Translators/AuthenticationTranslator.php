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
 * Automatically responds to protocol-level authentication events.
 *
 * Handles the mandatory handshake sequence that must always occur
 * on every connection, regardless of script context:
 * sending credentials on connection and joining the initial area on successful login.
 */
class AuthenticationTranslator implements TranslatorInterface
{
    /**
     * @param string $username The username sent to the server during authentication.
     * @param string $token The authentication token (password/ticket) sent during authentication.
     */
    public function __construct(
        private readonly string $username,
        #[\SensitiveParameter]
        private readonly string $token,
    ) {}

    /**
     * Reacts automatically to authentication events.
     *
     * - {@see AqwSocketClient\Events\ConnectionEstablishedEvent}: Responds with a {@see AqwSocketClient\Commands\LoginCommand} using the stored credentials.
     * - {@see AqwSocketClient\Events\LoginRespondedEvent}: Responds with a {@see AqwSocketClient\Commands\JoinInitialAreaCommand} if login was successful.
     *
     * @return CommandInterface|null The protocol response command, or **null** if no automatic reaction is needed.
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
