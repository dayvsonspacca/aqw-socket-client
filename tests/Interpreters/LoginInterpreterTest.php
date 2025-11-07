<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Events\LoginResponseEvent;
use AqwSocketClient\Interpreters\LoginInterpreter;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LoginInterpreterTest extends TestCase
{
    private readonly LoginInterpreter $interpreter;

    protected function setUp(): void
    {
        $this->interpreter = new LoginInterpreter();
    }

    #[Test]
    public function should_interpreter_connection_estabilished(): void
    {
        $message   = XmlMessage::fromString("<cross-domain-policy><allow-access-from domain='*' to-ports='5591' /></cross-domain-policy>");

        $events = $this->interpreter->interpret($message);

        $this->assertIsArray($events);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(ConnectionEstabilishedEvent::class, $events[0]);
    }


    #[Test]
    public function should_interpreter_login_response_event(): void
    {
        $message   = DelimitedMessage::fromString('%xt%loginResponse%-1%true%43472%made2903%%2025-11-07T10:38:12%sNews=995,sMap=news/Map-UI_r38.swf,sBook=news/spiderbook3.swf,sAssets=Assets_20251014.swf,gMenu=dynamic-gameMenu-17Jan22.swf,sVersion=R0038,QSInfo=519,iMaxBagSlots=500,iMaxBankSlots=900,iMaxHouseSlots=300,iMaxGuildMembers=800,iMaxFriends=300,iMaxLoadoutSlots=50%3.01%');

        $events = $this->interpreter->interpret($message);

        $this->assertIsArray($events);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(LoginResponseEvent::class, $events[0]);
    }
}