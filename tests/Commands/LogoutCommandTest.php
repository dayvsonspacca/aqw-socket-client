<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Commands\LogoutCommand;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LogoutCommandTest extends TestCase
{
    private readonly LogoutCommand $command;

    protected function setUp(): void
    {
        $this->command = new LogoutCommand();
    }

    #[Test]
    public function should_create_logout_command(): void
    {
        $this->assertInstanceOf(LogoutCommand::class, $this->command);
    }

    #[Test]
    public function should_pack_correct_packet(): void
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame("%xt%zm%cmd%1%logout%\u{0000}", $packet->unpacketify());
    }
}