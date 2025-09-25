<?php

declare(strict_types=1);

namespace AqwSocketClient\Exceptions;

use Exception;

class PacketException extends Exception
{
    public static function emptyPacket(): PacketException
    {
        return new self('The data of a packet cant be empty.');
    }
}
