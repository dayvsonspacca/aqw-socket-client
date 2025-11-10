<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Events\{JoinedAreaEvent};
use AqwSocketClient\Interpreters\PlayerRelatedInterpreter;
use AqwSocketClient\Messages\{JsonMessage};
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PlayerRelatedInterpreterTest extends TestCase
{
    private readonly PlayerRelatedInterpreter $interpreter;

    protected function setUp(): void
    {
        $this->interpreter = new PlayerRelatedInterpreter();
    }

    #[Test]
    public function should_interpreter_joined_area_event(): void
    {
        $message   = JsonMessage::fromString('{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"battleon-1","uoBranch":[{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18407,"strPad":"Spawn","intMPMax":100,"intHP":3085,"afk":true,"intHPMax":3085,"ty":481,"tx":355,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Gustavo Figueiredo","uoName":"gustavo figueiredo"},{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18446,"strPad":"Spawn","intMPMax":100,"intHP":4905,"afk":true,"intHPMax":4905,"ty":330,"tx":875,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"jonnysmeik157","uoName":"jonnysmeik157"},{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18775,"strPad":"Spawn","intMPMax":100,"intHP":4850,"afk":true,"intHPMax":4850,"ty":328,"tx":136,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Seyren","uoName":"seyren"},{"strFrame":"Enter","intMP":100,"intLevel":100,"entID":18814,"strPad":"Spawn","intMPMax":100,"intHP":2835,"afk":true,"intHPMax":2835,"ty":469,"tx":521,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Raposa Rukia","uoName":"raposa rukia"},{"strFrame":"Enter","intMP":100,"intLevel":62,"entID":18850,"strPad":"Spawn","intMPMax":100,"intHP":2261,"afk":true,"intHPMax":2261,"ty":306,"tx":700,"intState":1,"entType":"p","showHelm":true,"showCloak":false,"strUsername":"Jaspion Aulas","uoName":"jaspion aulas"},{"strFrame":"Enter2","intMP":100,"intLevel":87,"entID":18896,"strPad":"Spawn","intMPMax":100,"intHP":2939,"afk":false,"intHPMax":2939,"ty":373,"tx":478,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"marlonbr123","uoName":"marlonbr123"},{"strFrame":"Enter","intMP":100,"intLevel":78,"entID":18916,"strPad":"Spawn","intMPMax":100,"intHP":3614,"afk":false,"intHPMax":3614,"ty":0,"tx":0,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"xcvn12","uoName":"xcvn12"},{"strFrame":"Enter","intMP":100,"intLevel":87,"entID":18917,"strPad":"Spawn","intMPMax":100,"intHP":3004,"afk":false,"intHPMax":3004,"ty":403,"tx":639,"intState":1,"entType":"p","showHelm":true,"showCloak":true,"strUsername":"Athylos","uoName":"athylos"},{"strFrame":"Enter","intMP":100,"intLevel":81,"entID":18925,"strPad":"Spawn","intMPMax":100,"intHP":500,"afk":false,"intHPMax":500,"ty":0,"tx":0,"intState":1,"entType":"p","showHelm":true,"showCloak":false,"strUsername":"made2903","uoName":"made2903"}],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"sExtra":"","areaId":3,"strMapName":"battleon"}}}');

        $events = $this->interpreter->interpret($message);

        $this->assertIsArray($events);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(JoinedAreaEvent::class, $events[0]);
        $this->assertSame($events[0]->mapName, 'battleon');
        $this->assertSame($events[0]->mapNumber, 1);
        $this->assertSame($events[0]->areaId, 3);
        $this->assertIsArray($events[0]->players);
        $this->assertCount(9, $events[0]->players);
    }

}
