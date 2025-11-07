<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PacketTest extends TestCase
{
    #[Test]
    public function packetify_adds_null_terminator(): void
    {
        $data   = 'hello';
        $packet = Packet::packetify($data);

        $this->assertSame("hello\0", $packet->unpacketify());
    }
}
