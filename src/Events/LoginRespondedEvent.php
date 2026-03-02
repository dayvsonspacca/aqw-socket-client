<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use Override;

/**
 * Represents the **response** received from the server after a client
 * attempts a login operation.
 *
 * This event signals whether the authentication was successful or failed.
 */
final class LoginRespondedEvent implements EventInterface
{
    public function __construct(
        public readonly bool $success,
        public readonly ?SocketIdentifier $socketId = null,
    ) {}

    /**
     * @return ?LoginRespondedEvent
     *
     * @throws InvalidArgumentException When socket id in data is negative or zero.
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof DelimitedMessage && $message->type === DelimitedMessageType::LoginResponse) {
            $success = (bool) $message->data[0];
            $socketId = $success ? new SocketIdentifier((int) $message->data[1]) : null;

            return new self($success, $socketId);
        }

        return null;
    }
}
