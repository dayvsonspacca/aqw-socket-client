<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Commands\JoinMapCommand;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JoinMapCommandTest extends TestCase
{
    #[Test]
    public function should_create_join_map_command(): void
    {
        $command = new JoinMapCommand('PlayerOne', 'battleon');

        $this->assertInstanceOf(JoinMapCommand::class, $command);
        $this->assertSame('PlayerOne', $command->username);
        $this->assertSame('battleon', $command->mapName);
        $this->assertSame(0, $command->room);
    }

    #[Test]
    public function should_pack_packet_without_room_when_room_is_zero(): void
    {
        $command = new JoinMapCommand('PlayerOne', 'battleon');
        $packet = $command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame("%xt%zm%cmd%1%tfer%PlayerOne%battleon%\u{0000}", $packet->unpacketify());
    }

    #[Test]
    public function should_pack_packet_with_room_when_room_is_provided(): void
    {
        $command = new JoinMapCommand('PlayerOne', 'battleon', 2);
        $packet = $command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame("%xt%zm%cmd%1%tfer%PlayerOne%battleon-2%\u{0000}", $packet->unpacketify());
    }
}
