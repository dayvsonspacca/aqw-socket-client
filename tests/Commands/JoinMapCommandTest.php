<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Commands\JoinMapCommand;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JoinMapCommandTest extends TestCase
{
    private readonly JoinMapCommand $command;

    protected function setUp(): void
    {
        $this->command = new JoinMapCommand('Hilise', 'yulgar', 5);
    }

    #[Test]
    public function should_create_join_map_command()
    {
        $this->assertInstanceOf(JoinMapCommand::class, $this->command);
    }

    #[Test]
    public function should_pack_to_join_command_with_room_number()
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame($packet->unpacketify(), "%xt%zm%cmd%1%tfer%Hilise%yulgar-5%\u{0000}");
    }


    #[Test]
    public function should_pack_to_join_command_without_room_number()
    {
        $command = new JoinMapCommand('Hilise', 'yulgar');
        $packet  = $command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame($packet->unpacketify(), "%xt%zm%cmd%1%tfer%Hilise%yulgar%\u{0000}");
    }
}
