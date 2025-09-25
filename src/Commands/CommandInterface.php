<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Packet;

interface CommandInterface
{
    /**
     * Transform a command to the packet version to send server.
     */
    public function toPacket(): Packet;
}