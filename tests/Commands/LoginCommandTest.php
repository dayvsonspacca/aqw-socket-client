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
        $username = 'Artix';
        $token = 'thisisnotartixtoken';

        $this->command = new LoginCommand($username, $token);
    }

    #[Test]
    public function should_create_login_command()
    {
        $this->assertInstanceOf(LoginCommand::class, $this->command);
    }

    #[Test]
    public function should_pack_to_correct_login_packet()
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame($packet->unpacketify(), "<msg t='sys'>" .
                "<body action='login' r='0'>" .
                "<login z='zone_master'>" .
                "<nick><![CDATA[SPIDER#0001~Artix~3.01]]></nick>" .
                "<pword><![CDATA[thisisnotartixtoken]]></pword>" .
                '</login></body></msg>'.  "\u{0000}");
    }
}