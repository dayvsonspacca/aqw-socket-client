<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginCommandTest extends TestCase
{
    private LoginCommand $command;
    private string $token;

    protected function setUp(): void
    {
        $this->token = md5(random_bytes(4));
        $this->command = new LoginCommand(username: 'PlayerOne', token: $this->token);
    }

    #[Test]
    public function should_create_login_command(): void
    {
        $this->assertInstanceOf(LoginCommand::class, $this->command);
        $this->assertSame('PlayerOne', $this->command->username);
        $this->assertSame($this->token, $this->command->token);
    }

    #[Test]
    public function should_pack_correct_packet(): void
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertStringContainsString('PlayerOne', $packet->unpacketify());
        $this->assertStringContainsString($this->token, $packet->unpacketify());
    }
}
