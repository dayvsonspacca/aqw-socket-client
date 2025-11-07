<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Enums\JsonCommandType;

/**
 * Represents a **single command entity** parsed from a complex JSON server message.
 *
 * This class serves as an internal structure to hold the command type and its
 * raw associated data after the {@see AqwSocketClient\Messages\JsonMessage} has been pre-processed.
 */
class JsonCommand
{
    /**
     * @param JsonCommandType $type The enumerated type of the specific JSON command.
     * @param array $data The raw array data associated with the command.
     */
    public function __construct(
        public readonly JsonCommandType $type,
        public readonly array $data
    ) {
    }
}
