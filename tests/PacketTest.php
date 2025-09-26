<?php

namespace AqwSocketClient\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use AqwSocketClient\Packet;
use AqwSocketClient\Exceptions\PacketException;

class PacketTest extends TestCase
{
    #[Test]
    public function packetify_adds_null_terminator(): void
    {
        $data = "hello";
        $packet = Packet::packetify($data);

        $this->assertSame("hello\0", $packet->unpacketify());
    }

    #[Test]
    public function packetify_throws_exception_on_empty_string(): void
    {
        $this->expectException(PacketException::class);
        Packet::packetify("");
    }
}
