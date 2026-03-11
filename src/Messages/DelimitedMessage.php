<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\MessageInterface;
use Override;
use Psl\Str;
use Psl\Vec;

/**
 * Represents a server message that is formatted using a **delimiter**
 * (e.g., the '%' character).
 *
 * This class handles the parsing of the delimited string, extracting the
 * message type and the subsequent data payload.
 */
final class DelimitedMessage implements MessageInterface
{
    /**
     * @param DelimitedMessageType $type The enumerated type of the delimited message.
     * @param array<int, string> $data An array containing the payload data of the message,
     * following the message type.
     */
    private function __construct(
        public readonly DelimitedMessageType $type,
        public readonly array $data,
        public readonly string $raw,
    ) {}

    /**
     * Attempts to create a DelimitedMessage object by parsing the raw string.
     *
     * The method validates the message format (starts and ends with '%'),
     * extracts the parts, identifies the message type, and separates the data payload.
     *
     * @param string $message The raw string data received from the socket.
     * @return DelimitedMessage|false The newly created message object, or **false**
     * if the message format is invalid or the message type cannot be determined.
     */
    #[Override]
    public static function from(string $message): DelimitedMessage|false
    {
        if (!Str\starts_with($message, '%') && !Str\ends_with($message, '%')) {
            return false;
        }

        $parts = Vec\filter(Str\split($message, '%'), static fn(string $part): bool => $part !== '');
        $parts = Vec\filter_with_key($parts, static fn(int $k, string $_): bool => $k !== 0 && $k !== 2);

        $type = DelimitedMessageType::tryFrom($parts[0]);
        if ($type === null) {
            return false;
        }

        return new self($type, Vec\slice($parts, 1), $message);
    }
}
