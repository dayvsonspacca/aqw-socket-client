<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

interface CommandInterface
{
    /**
     * Transform a command to the packet version to send server.
     */
    public function toPacket(): string;
}