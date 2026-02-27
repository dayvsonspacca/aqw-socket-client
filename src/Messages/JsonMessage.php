<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\MessageInterface;
use Override;

/**
 * Represents a parsed socket message that is structured as a **single JSON object**.
 *
 * This class is responsible for decoding the raw string data received from the
 * server and extracting the core message details: its type and raw associated data.
 */
final class JsonMessage implements MessageInterface
{
    /**
     * Constructs the JsonMessage object with the parsed message type and data.
     *
     * @param JsonMessageType $type The enumerated type identifying the action requested/reported.
     * @param array $data The raw array structure associated with this message's content.
     */
    private function __construct(
        public readonly JsonMessageType $type,
        public readonly array $data,
        public readonly string $raw,
    ) {}

    /**
     * Attempts to create a JsonMessage object by decoding the raw JSON string received from the socket.
     *
     * This factory method handles the necessary checks and extraction logic to transform
     * the raw message into a structured object.
     *
     * @param string $message The raw string data received from the socket (expected to be a single JSON object).
     * @return JsonMessage|false The successfully created message object, or **false** if parsing fails
     * due to invalid JSON, missing required fields, or an unknown message type.
     * @mago-ignore analyzer:mixed-assignment
     */
    #[Override]
    public static function from(string $message): JsonMessage|false
    {
        $json = json_decode($message, true);

        if (!is_array($json)) {
            return false;
        }

        $b = $json['b'] ?? null;
        if (!is_array($b)) {
            return false;
        }

        $messageData = $b['o'] ?? null;
        if (!is_array($messageData)) {
            return false;
        }

        $cmd = $messageData['cmd'] ?? null;
        if (!is_string($cmd)) {
            return false;
        }

        $type = JsonMessageType::from($cmd);
        if (!$type) {
            return false;
        }

        return new self($type, $messageData, $message);
    }
}
