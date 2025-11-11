<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Events\{JoinedAreaEvent, PlayerInventoryLoadedEvent};
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

    #[Test]
    public function should_interpreter_inventory_loaded_event(): void
    {
        $message = JsonMessage::fromString('{"t":"xt","b":{"r":-1,"o":{"bankCount":0,"cmd":"loadInventoryBig","items":[{"ItemID":3,"sElmt":"None","sLink":"","bExtra2":0,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":0,"sES":"Weapon","bExtra1":0,"bWear":0,"sType":"Staff","EnhLvl":1,"metaValues":{},"iCost":100,"EnhPatternID":1,"iRty":13,"iQSValue":0,"iQty":1,"sReqQuests":"","iLvl":1,"sIcon":"iwstaff","iEnh":1856,"bTemp":0,"ProcID":"","CharItemID":1.073108779E9,"bPTR":0,"iHrs":769,"sFile":"items/staves/staff01.swf","iQSIndex":-1,"EnhID":1856,"EnhDPS":100,"sDesc":"Staff","iStk":1,"bBank":0,"EnhRty":1,"bEquip":1,"bHouse":0,"bUpg":0,"EnhRng":10,"sName":"Default Staff"},{"ItemID":15651,"sElmt":"None","sLink":"NewHealerB2","bExtra2":0,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":1,"sES":"ar","bExtra1":0,"sType":"Class","metaValues":{"ref":17},"EnhLvl":1,"iCost":0,"EnhPatternID":1,"iRty":12,"iQSValue":0,"iQty":766,"sReqQuests":"","iLvl":1,"sIcon":"iiclass","iEnh":1959,"bTemp":0,"ProcID":"","CharItemID":1.07310878E9,"bPTR":0,"iHrs":769,"sFile":"NewHealerR2.swf","iQSIndex":-1,"EnhID":1959,"EnhDPS":100,"sDesc":"Recommended enhancement: Healer. Healers are servants of good who use their powers to aid the sick, weak, and injured. Their powerful healing magic is often the difference between a group\'s victory or doom.","iStk":1,"bBank":0,"EnhRty":1,"bEquip":1,"bHouse":0,"bUpg":0,"EnhRng":10,"sName":"Healer","sMeta":17},{"ItemID":45739,"sElmt":"None","sLink":"","bExtra2":1,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":1,"sES":"None","bExtra1":0,"metaValues":{"nosell":true},"sType":"Resource","iCost":0,"iRty":13,"iQSValue":0,"iQty":2,"sReqQuests":"","sIcon":"iibag","iLvl":1,"bTemp":0,"CharItemID":1.0731091E9,"bPTR":0,"iHrs":769,"iQSIndex":-1,"EnhID":0,"iStk":3,"sDesc":"Collect 3 of these to earn a free spin at the Wheel of Doom!","bBank":0,"bHouse":0,"bUpg":0,"bEquip":0,"sName":"Gear of Doom","sMeta":"NoSell"}],"hitems":[]}}}');

        $events = $this->interpreter->interpret($message);
        $this->assertIsArray($events);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(PlayerInventoryLoadedEvent::class, $events[0]);
        $this->assertIsArray($events[0]->items);
        $this->assertCount(3, $events[0]->items);
    }
}
