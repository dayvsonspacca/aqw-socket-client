<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginCommandTest extends TestCase
{
    private LoginCommand $command;
    private string $token;
    private PlayerName $playerName;

    protected function setUp(): void
    {
        $this->token = md5(random_bytes(4));
        $this->playerName = new PlayerName('Hilise');
        $this->command = new LoginCommand($this->playerName, token: $this->token);
    }

    #[Test]
    public function it_creates_command(): void
    {
        $this->assertInstanceOf(LoginCommand::class, $this->command);
        $this->assertSame($this->playerName, $this->command->playerName);
        $this->assertSame($this->token, $this->command->token);
    }

    #[Test]
    public function should_pack_correct_packet(): void
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertStringContainsString($this->playerName->value, $packet->unpacketify());
        $this->assertStringContainsString($this->token, $packet->unpacketify());
    }
}
