<?php

declare(strict_types=1);

namespace AqwSocketClient\Exceptions;

use Exception;

/**
 * Exception thrown when there is an error related to packet creation or handling.
 *
 * This class provides specific factory methods for common packet errors.
 */
class PacketException extends Exception
{
    /**
     * Creates an exception for the case when a packet's data is empty.
     *
     * @return PacketException The exception instance indicating an empty packet.
     */
    public static function emptyPacket(): PacketException
    {
        return new self('The data of a packet cant be empty.');
    }
}
