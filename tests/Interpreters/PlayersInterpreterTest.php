<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Events\LoginResponseEvent;
use AqwSocketClient\Events\PlayerDetectedEvent;
use AqwSocketClient\Interpreters\PlayersInterpreter;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PlayersInterpreterTest extends TestCase
{
    private readonly PlayersInterpreter $interpreter;

    protected function setUp(): void
    {
        $this->interpreter = new PlayersInterpreter();
    }

    #[Test]
    public function should_interpreter_player_detected_via_exit_area_message(): void
    {
        $message   = DelimitedMessage::fromString('%xt%exitArea%-1%213839%authur%');

        $events = $this->interpreter->interpret($message);

        $this->assertIsArray($events);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(PlayerDetectedEvent::class, $events[0]);
        $this->assertSame($events[0]->name, 'authur');
    }


    #[Test]
    public function should_interpreter_player_detected_via_player_change(): void
    {
        $message   = DelimitedMessage::fromString('%xt%uotls%-1%ninjie_ninjie%sp:8,tx:224,ty:354,strFrame:Enter%');

        $events = $this->interpreter->interpret($message);

        $this->assertIsArray($events);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(PlayerDetectedEvent::class, $events[0]);
        $this->assertSame($events[0]->name, 'ninjie_ninjie');
    }
}