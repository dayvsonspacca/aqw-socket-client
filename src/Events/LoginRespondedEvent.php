<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Objects\SocketIdentifier;
use Override;

/**
 * Represents the **response** received from the server after a client
 * attempts a login operation.
 *
 * This event signals whether the authentication was successful or failed.
 */
final class LoginRespondedEvent implements EventInterface
{
    /**
     * @param bool $success Indicates whether the login attempt was successful (**true**) or failed (**false**).
     * @param SocketIdentifier $socketId The **socket ID** for the current connection. This is a **temporary** identifier assigned by the server, distinct from the permanent user account ID. It changes with every new connection.
     */
    public function __construct(
        public readonly bool $success,
        public readonly SocketIdentifier $socketId,
    ) {}

    /**
     * @return ?LoginRespondedEvent
     *
     * @throws InvalidArgumentException When socket id in data is negative or zero.
     * @mago-ignore analyzer:possibly-undefined-array-index
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof DelimitedMessage && $message->type === DelimitedMessageType::LoginResponse) {
            return new self((bool) $message->data[0], new SocketIdentifier((int) $message->data[1]));
        }

        return null;
    }
}
