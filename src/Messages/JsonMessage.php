<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\MessageInterface;

/**
 * Represents a parsed socket message that is structured as a **single JSON object**.
 *
 * This class is responsible for decoding the raw string data received from the
 * server and extracting the core message details: its type and raw associated data.
 */
class JsonMessage implements MessageInterface
{
    /**
     * Constructs the JsonMessage object with the parsed message type and data.
     *
     * @param JsonMessageType $type The enumerated type identifying the action requested/reported.
     * @param array $data The raw array structure associated with this message's content.
     */
    private function __construct(
        public readonly JsonMessageType $type,
        public readonly array $data
    ) {
    }

    /**
     * Attempts to create a JsonMessage object by decoding the raw JSON string received from the socket.
     *
     * This factory method handles the necessary checks and extraction logic to transform
     * the raw message into a structured object.
     *
     * @param string $message The raw string data received from the socket (expected to be a single JSON object).
     * @return JsonMessage|false The successfully created message object, or **false** if parsing fails
     * due to invalid JSON, missing required fields, or an unknown message type.
     */
    public static function fromString(string $message): JsonMessage|false
    {
        $json = json_decode($message, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            return false;
        }

        if (!isset($json['b']['o']) || !is_array($json['b']['o'])) {
            return false;
        }

        $messageData = $json['b']['o'];

        if (!isset($messageData['cmd']) || !is_string($messageData['cmd'])) {
            return false;
        }

        $type = JsonMessageType::fromString($messageData['cmd']);
        if (!$type) {
            return false;
        }

        return new self($type, $messageData);
    }
}
