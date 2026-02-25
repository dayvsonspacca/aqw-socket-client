<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Interpreters;

use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interpreters\PlayerInterpreter;
use AqwSocketClient\Messages\JsonMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerInterpreterTest extends TestCase
{
    private readonly PlayerInterpreter $interpreter;

    protected function setUp(): void
    {
        $this->interpreter = new PlayerInterpreter();
    }

    #[Test]
    public function should_interpret_inventory_loaded_event(): void
    {
        $message = JsonMessage::fromString(
            '{"t":"xt","b":{"r":-1,"o":{"bankCount":0,"cmd":"loadInventoryBig","items":[{"ItemID":3,"sElmt":"None","sLink":"","bExtra2":0,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":0,"sES":"Weapon","bExtra1":0,"bWear":0,"sType":"Staff","EnhLvl":1,"metaValues":{},"iCost":100,"EnhPatternID":1,"iRty":13,"iQSValue":0,"iQty":1,"sReqQuests":"","iLvl":1,"sIcon":"iwstaff","iEnh":1856,"bTemp":0,"ProcID":"","CharItemID":1.073108779E9,"bPTR":0,"iHrs":769,"sFile":"items/staves/staff01.swf","iQSIndex":-1,"EnhID":1856,"EnhDPS":100,"sDesc":"Staff","iStk":1,"bBank":0,"EnhRty":1,"bEquip":1,"bHouse":0,"bUpg":0,"EnhRng":10,"sName":"Default Staff"},{"ItemID":15651,"sElmt":"None","sLink":"NewHealerB2","bExtra2":0,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":1,"sES":"ar","bExtra1":0,"sType":"Class","metaValues":{"ref":17},"EnhLvl":1,"iCost":0,"EnhPatternID":1,"iRty":12,"iQSValue":0,"iQty":766,"sReqQuests":"","iLvl":1,"sIcon":"iiclass","iEnh":1959,"bTemp":0,"ProcID":"","CharItemID":1.07310878E9,"bPTR":0,"iHrs":769,"sFile":"NewHealerR2.swf","iQSIndex":-1,"EnhID":1959,"EnhDPS":100,"sDesc":"Recommended enhancement: Healer. Healers are servants of good who use their powers to aid the sick, weak, and injured. Their powerful healing magic is often the difference between a group\'s victory or doom.","iStk":1,"bBank":0,"EnhRty":1,"bEquip":1,"bHouse":0,"bUpg":0,"EnhRng":10,"sName":"Healer","sMeta":17},{"ItemID":45739,"sElmt":"None","sLink":"","bExtra2":1,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":1,"sES":"None","bExtra1":0,"metaValues":{"nosell":true},"sType":"Resource","iCost":0,"iRty":13,"iQSValue":0,"iQty":2,"sReqQuests":"","sIcon":"iibag","iLvl":1,"bTemp":0,"CharItemID":1.0731091E9,"bPTR":0,"iHrs":769,"iQSIndex":-1,"EnhID":0,"iStk":3,"sDesc":"Collect 3 of these to earn a free spin at the Wheel of Doom!","bBank":0,"bHouse":0,"bUpg":0,"bEquip":0,"sName":"Gear of Doom","sMeta":"NoSell"}],"hitems":[]}}}',
        );

        $events = $this->interpreter->interpret($message);
        $this->assertIsArray($events);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(PlayerInventoryLoadedEvent::class, $events[0]);
        $this->assertIsArray($events[0]->items);
        $this->assertCount(3, $events[0]->items);
    }
}
