<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Enums\JsonCommandType;
use AqwSocketClient\Interfaces\MessageInterface;

/**
 * Represents a server message that is formatted as one or more
 * **JSON objects** concatenated together.
 *
 * This class handles the necessary pre-processing (wrapping the objects in an array)
 * to correctly parse the JSON into an array of internal command structures.
 */
class JsonMessage implements MessageInterface
{
    /**
     * @param JsonCommand[] $commands An array of parsed internal JsonCommand objects
     * containing the type and raw data.
     */
    private function __construct(
        public readonly array $commands
    ) {}

    /**
     * Attempts to create a JsonMessage object by preprocessing the raw string
     * and decoding the resulting JSON.
     *
     * The method first transforms the concatenated JSON objects into a valid array
     * structure for decoding, then maps the raw parts into internal command objects.
     *
     * @param string $message The raw string data received from the socket.
     * @return JsonMessage|false The newly created message object containing the
     * parsed commands, or **false** on JSON decoding failure or if no commands are found.
     */
    public static function fromString(string $message): JsonMessage|false
    {
        $message = preg_replace('/}\s*{/', '},{', $message);
        $message = '[' . $message . ']';

        $json = json_decode($message, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $json = array_map(fn($part) => $part['b']['o'], $json);
        $json = array_map(function ($part) {
            $type = JsonCommandType::fromString($part['cmd']);
            if (!$type) {
                return false;
            }

            return new JsonCommand($type, $part);
        }, $json);

        $json = array_filter($json, fn($command) => $command);
        
        if (empty($json)) {
            return false;
        }

        return new self($json);
    }
}