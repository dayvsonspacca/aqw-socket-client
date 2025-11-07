<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

/**
 * Defines the possible types for messages that use the **delimited format** (e.g., using '%').
 *
 * This enum maps the raw string identifier from the server message to a
 * strongly-typed case for easy processing.
 */
enum DelimitedMessageType
{
    /**
     * General server message, often containing status or initialization data.
     */
    case Server;

    /**
     * Response from the server indicating the outcome of a login attempt.
     * Maps to server string: `loginResponse`.
     */
    case LoginResponse;

    /**
     * Message signaling that a player has left the current area/screen.
     * Maps to server string: `exitArea`.
     */
    case ExitArea;

    /**
     * Message signaling a change in player state or the detection of a new player.
     * Maps to server string: `uotls`.
     */
    case PlayerChange;

    /**
     * Creates an enum case from the raw string identifier found in the delimited message.
     *
     * @param string $string The raw message type identifier (e.g., 'uotls', 'loginResponse').
     * @return self|false The corresponding enum case, or **false** if the string is unknown.
     */
    public static function fromString(string $string): self|false
    {
        return match ($string) {
            'loginResponse' => self::LoginResponse,
            'server' => self::Server,
            'exitArea' => self::ExitArea,
            'uotls' => self::PlayerChange,
            default => false
        };
    }
}
