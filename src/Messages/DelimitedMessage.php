<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\MessageInterface;
use Override;

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
        if (!str_starts_with($message, '%') && !str_ends_with($message, '%')) {
            return false;
        }

        $parts = explode('%', $message);
        $parts = array_filter($parts, static fn($part) => !(mb_strlen($part) === 0));

        unset($parts[1]);
        unset($parts[3]);

        $parts = array_values($parts);

        $type = DelimitedMessageType::tryFrom($parts[0]);
        if ($type === null) {
            return false;
        }

        $data = array_filter($parts, static fn($key) => $key !== 0, ARRAY_FILTER_USE_KEY);

        return new self($type, array_values($data), $message);
    }
}
