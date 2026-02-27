<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Messages\XmlMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConnectionEstablishedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = XmlMessage::from(
            "<cross-domain-policy><allow-access-from domain='*' to-ports='5588' /></cross-domain-policy>",
        );

        /** @var XmlMessage $message */
        $event = ConnectionEstablishedEvent::from($message);

        $this->assertInstanceOf(ConnectionEstablishedEvent::class, $event);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"battleon-1","uoBranch":[],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"mondef":[],"areaId":3,"strMapName":"battleon"}}}',
        );

        /** @var JsonMessage $message */
        $event = ConnectionEstablishedEvent::from($message);

        $this->assertNull($event);
    }
}
