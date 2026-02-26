<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\AreaIdentifier;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AreaJoinedEventTest extends TestCase
{
    private AreaJoinedEvent $event;

    protected function setUp(): void
    {
        $this->event = new AreaJoinedEvent(
            'battleon',
            1,
            new AreaIdentifier(42),
            [['socket_id' => 1, 'name' => 'PlayerOne'], ['socket_id' => 2, 'name' => 'PlayerTwo']],
            [[
                'name' => 'Goblin',
                'race' => 'Goblin',
                'asset_name' => 'goblin_asset',
                'level' => 5,
                'hp' => 100,
            ]],
        );
    }

    #[Test]
    public function should_create_area_joined_event(): void
    {
        $this->assertInstanceOf(AreaJoinedEvent::class, $this->event);
        $this->assertSame('battleon', $this->event->mapName);
        $this->assertSame(1, $this->event->mapNumber);
        $this->assertSame(42, $this->event->areaId->value);
        $this->assertSame(
            [['socket_id' => 1, 'name' => 'PlayerOne'], ['socket_id' => 2, 'name' => 'PlayerTwo']],
            $this->event->players,
        );
        $this->assertSame(
            [[
                'name' => 'Goblin',
                'race' => 'Goblin',
                'asset_name' => 'goblin_asset',
                'level' => 5,
                'hp' => 100,
            ]],
            $this->event->monsters,
        );
    }

    #[Test]
    public function should_return_null_when_json_message_type_is_not_area_joind(): void
    {
        $message = JsonMessage::fromString(
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"equipItem","areaName":"battleon-1","uoBranch":[{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18407,"strPad":"Spawn","intMPMax":100,"intHP":3085,"afk":true,"intHPMax":3085,"ty":481,"tx":355,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Gustavo Figueiredo","uoName":"gustavo figueiredo"},{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18446,"strPad":"Spawn","intMPMax":100,"intHP":4905,"afk":true,"intHPMax":4905,"ty":330,"tx":875,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"jonnysmeik157","uoName":"jonnysmeik157"},{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18775,"strPad":"Spawn","intMPMax":100,"intHP":4850,"afk":true,"intHPMax":4850,"ty":328,"tx":136,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Seyren","uoName":"seyren"},{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18814,"strPad":"Spawn","intMPMax":100,"intHP":2835,"afk":true,"intHPMax":2835,"ty":469,"tx":521,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Raposa Rukia","uoName":"raposa rukia"},{"strFrame":"Enter","intMP":100,"intLevel":62,"entID":18850,"strPad":"Spawn","intMPMax":100,"intHP":2261,"afk":true,"intHPMax":2261,"ty":306,"tx":700,"intState":1,"entType":"p","showHelm":true,"showCloak":false,"strUsername":"Jaspion Aulas","uoName":"jaspion aulas"},{"strFrame":"Enter2","intMP":100,"intLevel":87,"entID":18896,"strPad":"Spawn","intMPMax":100,"intHP":2939,"afk":false,"intHPMax":2939,"ty":373,"tx":478,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"marlonbr123","uoName":"marlonbr123"},{"strFrame":"Enter","intMP":100,"intLevel":78,"entID":18916,"strPad":"Spawn","intMPMax":100,"intHP":3614,"afk":false,"intHPMax":3614,"ty":0,"tx":0,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"xcvn12","uoName":"xcvn12"},{"strFrame":"Enter","intMP":100,"intLevel":87,"entID":18917,"strPad":"Spawn","intMPMax":100,"intHP":3004,"afk":false,"intHPMax":3004,"ty":403,"tx":639,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Athylos","uoName":"athylos"},{"strFrame":"Enter","intMP":100,"intLevel":81,"entID":18925,"strPad":"Spawn","intMPMax":100,"intHP":500,"afk":false,"intHPMax":500,"ty":0,"tx":0,"intState":1,"entType":"p","showHelm":true,"showCloak":false,"strUsername":"made2903","uoName":"made2903"}],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"sExtra":"","areaId":3,"strMapName":"battleon"}}}',
        );
        /** @var JsonMessage $message */
        $event = AreaJoinedEvent::fromJsonMessage($message);

        $this->assertNull($event);
    }

    #[Test]
    public function it_creates_area_joined_event_when_json_message_correct(): void
    {
        $message = JsonMessage::fromString(
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"battleon-1","uoBranch":[{"entID":18407,"strUsername":"Gustavo Figueiredo"}],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[{"MonID":"1","intHPMax":"10"}],"mondef":[{"MonID":"1","strMonName":"strMonName","strMonFileName":"strMonFileName","intLevel":"intLevel","sRace":"sRace"}],"areaId":3,"strMapName":"battleon"}}}',
        );
        /** @var JsonMessage $message */
        $event = AreaJoinedEvent::fromJsonMessage($message);

        $this->assertInstanceOf(AreaJoinedEvent::class, $event);
        $this->assertSame($event->mapName, 'battleon');
        $this->assertCount(1, $event->monsters);
        $this->assertCount(1, $event->players);
    }
}
