<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

/**
 * Defines the possible types for messages that use the **delimited format** (e.g., using '%').
 */
enum DelimitedMessageType: string
{
    /**
     * General server message, often containing status or initialization data.
     */
    case Server = 'server';

    /**
     * Response from the server indicating the outcome of a login attempt.
     */
    case LoginResponse = 'loginResponse';

    /**
     * Message signaling that a player has left the current area/screen.
     */
    case ExitArea = 'exitArea';

    /**
     * Message signaling a change in player state or the detection of a new player.
     */
    case PlayerChange = 'uotls';

    /**
     * Message related to server warnings
     */
    case Warning = 'warning';
}
