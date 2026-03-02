<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Commands\JoinAreaCommand;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JoinAreaCommandTest extends TestCase
{
    private PlayerName $playerName;
    private AreaName $areaName;
    private RoomIdentifier $room;
    private JoinAreaCommand $command;

    protected function setUp(): void
    {
        $this->playerName = new PlayerName('Hilise');
        $this->areaName = new AreaName('battleon');
        $this->room = new RoomIdentifier(1);

        $this->command = new JoinAreaCommand($this->playerName, $this->areaName, $this->room);
    }

    #[Test]
    public function it_creates_command(): void
    {
        $this->assertInstanceOf(JoinAreaCommand::class, $this->command);

        $this->assertSame($this->playerName, $this->command->playerName);
        $this->assertSame($this->areaName, $this->command->areaName);
        $this->assertSame($this->room, $this->command->room);
    }

    #[Test]
    public function should_pack_packet_without_room_when_room_is_zero(): void
    {
        $command = new JoinAreaCommand($this->playerName, $this->areaName);
        $packet = $command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame("%xt%zm%cmd%1%tfer%Hilise%battleon%\u{0000}", $packet->unpacketify());
    }

    #[Test]
    public function should_pack_packet_with_room_when_room_is_provided(): void
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame("%xt%zm%cmd%1%tfer%Hilise%battleon-1%\u{0000}", $packet->unpacketify());
    }
}
