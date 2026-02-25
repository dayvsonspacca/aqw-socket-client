<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Messages\JsonMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerInventoryLoadedEventTest extends TestCase
{
    private PlayerInventoryLoadedEvent $event;

    protected function setUp(): void
    {
        $this->event = new PlayerInventoryLoadedEvent([
            ['name' => 'Iron Sword', 'description' => 'a sword', 'type' => 'weapon', 'file_name' => 'sword.swf'],
            ['name' => 'Health Potion', 'description' => 'a sword', 'type' => 'weapon', 'file_name' => 'sword.swf'],
        ]);
    }

    #[Test]
    public function should_create_player_inventory_loaded_event(): void
    {
        $this->assertInstanceOf(PlayerInventoryLoadedEvent::class, $this->event);
        $this->assertCount(2, $this->event->items);
        $this->assertSame('Iron Sword', $this->event->items[0]['name']);
        $this->assertSame('Health Potion', $this->event->items[1]['name']);
    }
    
    #[Test]
    public function should_return_null_when_json_message_type_is_not_inventory_loaded(): void
    {
        $message = JsonMessage::fromString(
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"equipItem","areaName":"battleon-1","uoBranch":[{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18407,"strPad":"Spawn","intMPMax":100,"intHP":3085,"afk":true,"intHPMax":3085,"ty":481,"tx":355,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Gustavo Figueiredo","uoName":"gustavo figueiredo"},{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18446,"strPad":"Spawn","intMPMax":100,"intHP":4905,"afk":true,"intHPMax":4905,"ty":330,"tx":875,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"jonnysmeik157","uoName":"jonnysmeik157"},{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18775,"strPad":"Spawn","intMPMax":100,"intHP":4850,"afk":true,"intHPMax":4850,"ty":328,"tx":136,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Seyren","uoName":"seyren"},{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18814,"strPad":"Spawn","intMPMax":100,"intHP":2835,"afk":true,"intHPMax":2835,"ty":469,"tx":521,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Raposa Rukia","uoName":"raposa rukia"},{"strFrame":"Enter","intMP":100,"intLevel":62,"entID":18850,"strPad":"Spawn","intMPMax":100,"intHP":2261,"afk":true,"intHPMax":2261,"ty":306,"tx":700,"intState":1,"entType":"p","showHelm":true,"showCloak":false,"strUsername":"Jaspion Aulas","uoName":"jaspion aulas"},{"strFrame":"Enter2","intMP":100,"intLevel":87,"entID":18896,"strPad":"Spawn","intMPMax":100,"intHP":2939,"afk":false,"intHPMax":2939,"ty":373,"tx":478,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"marlonbr123","uoName":"marlonbr123"},{"strFrame":"Enter","intMP":100,"intLevel":78,"entID":18916,"strPad":"Spawn","intMPMax":100,"intHP":3614,"afk":false,"intHPMax":3614,"ty":0,"tx":0,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"xcvn12","uoName":"xcvn12"},{"strFrame":"Enter","intMP":100,"intLevel":87,"entID":18917,"strPad":"Spawn","intMPMax":100,"intHP":3004,"afk":false,"intHPMax":3004,"ty":403,"tx":639,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Athylos","uoName":"athylos"},{"strFrame":"Enter","intMP":100,"intLevel":81,"entID":18925,"strPad":"Spawn","intMPMax":100,"intHP":500,"afk":false,"intHPMax":500,"ty":0,"tx":0,"intState":1,"entType":"p","showHelm":true,"showCloak":false,"strUsername":"made2903","uoName":"made2903"}],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"sExtra":"","areaId":3,"strMapName":"battleon"}}}',
        );
        /** @var JsonMessage $message */
        $event = PlayerInventoryLoadedEvent::fromJsonMessage($message);

        $this->assertNull($event);
    }
}
