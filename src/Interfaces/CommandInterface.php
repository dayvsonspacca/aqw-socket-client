<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

use AqwSocketClient\Packet;

interface CommandInterface
{
    public function pack(): Packet;
}