<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginCommandTest extends TestCase
{
    private readonly LoginCommand $command;

    protected function setUp(): void
    {
        $this->command = new LoginCommand(username: 'PlayerOne', token: 'abc123');
    }

    #[Test]
    public function should_create_login_command(): void
    {
        $this->assertInstanceOf(LoginCommand::class, $this->command);
        $this->assertSame('PlayerOne', $this->command->username);
        $this->assertSame('abc123', $this->command->token);
    }

    #[Test]
    public function should_pack_correct_packet(): void
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertStringContainsString('PlayerOne', $packet->unpacketify());
        $this->assertStringContainsString('abc123', $packet->unpacketify());
    }
}
