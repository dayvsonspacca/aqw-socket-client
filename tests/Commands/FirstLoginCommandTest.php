<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Commands\FirstLoginCommand;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FirstLoginCommandTest extends TestCase
{
    private readonly FirstLoginCommand $command;

    protected function setUp(): void
    {
        $this->command = new FirstLoginCommand();
    }

    #[Test]
    public function should_create_first_login_command()
    {
        $this->assertInstanceOf(FirstLoginCommand::class, $this->command);
    }

    #[Test]
    public function should_pack_to_correct_first_login_packet()
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame($packet->unpacketify(), "%xt%zm%firstJoin%1%\u{0000}");
    }
}