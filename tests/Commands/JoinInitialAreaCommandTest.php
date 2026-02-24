<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JoinInitialAreaCommandTest extends TestCase
{
    private readonly JoinInitialAreaCommand $command;

    protected function setUp(): void
    {
        $this->command = new JoinInitialAreaCommand();
    }

    #[Test]
    public function should_create_join_initial_area_command(): void
    {
        $this->assertInstanceOf(JoinInitialAreaCommand::class, $this->command);
    }

    #[Test]
    public function should_pack_correct_packet(): void
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame("%xt%zm%firstJoin%1%\u{0000}", $packet->unpacketify());
    }
}
