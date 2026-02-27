<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\JsonMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginRespondedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = DelimitedMessage::from(
            '%xt%loginResponse%-1%true%1080%madew29021%%2026-02-26T19:33:21%sNews=1078,sMap=news/Map-UI_r38.swf,sBook=news/spiderbook3.swf,sAssets=Assets_20251205.swf,gMenu=dynamic-gameMenu-17Jan22.swf,sVersion=R0039,QSInfo=519,iMaxBagSlots=500,iMaxBankSlots=900,iMaxHouseSlots=300,iMaxGuildMembers=800,iMaxFriends=300,iMaxLoadoutSlots=50%3.0141%',
        );

        /** @var DelimitedMessage $message */
        $event = LoginRespondedEvent::from($message);

        $this->assertInstanceOf(LoginRespondedEvent::class, $event);
        $this->assertSame($event->socketId->value, 1080);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"equipItem","areaName":"battleon-1","uoBranch":[],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"sExtra":"","areaId":3,"strMapName":"battleon"}}}',
        );

        /** @var JsonMessage $message */
        $event = LoginRespondedEvent::from($message);

        $this->assertNull($event);
    }
}
