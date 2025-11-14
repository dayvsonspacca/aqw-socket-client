<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Events\{ShopLoadedEvent};
use AqwSocketClient\Interpreters\ShopInterpreter;
use AqwSocketClient\Messages\{JsonMessage};
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ShopInterpreterTest extends TestCase
{
    private readonly ShopInterpreter $interpreter;

    protected function setUp(): void
    {
        $this->interpreter = new ShopInterpreter();
    }

    #[Test]
    public function should_interpreter_loaded_shop_event(): void
    {
        $message   = JsonMessage::fromString('{"t":"xt","b":{"r":-1,"o":{"shopinfo":{"bUpgrd":"0","items":[{"ItemID":"47482","sFaction":"None","iClass":"0","sElmt":"None","sLink":"ULS","bStaff":"0","iRng":"10","iDPS":"100","bCoins":"1","sES":"co","sType":"Armor","iCost":750,"iRty":"13","iQty":1,"sIcon":"iwarmor","iLvl":"1","FactionID":"1","bTemp":"0","iQtyRemain":"-1","iReqRep":"0","iQSvalue":"0","ShopItemID":"28150","sFile":"ULS.swf","EnhID":"0","iStk":"1","sDesc":"The parasite infecting this armor was created especially for Dage\'s Legion. Now truly be one with the Dark Lord.","bHouse":"0","bUpg":"0","iReqCP":"0","sName":"Legion Symbiote","iQSindex":"-1"}],"sField":"","ShopID":"216","bStaff":"0","bHouse":"0","Location":"Menu","iIndex":"-1","sName":"Undead Legion Shop"},"cmd":"loadShop"}}}');
        $events    = $this->interpreter->interpret($message);

        $this->assertIsArray($events);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(ShopLoadedEvent::class, $events[0]);
        $this->assertSame($events[0]->shopId, 216);
        $this->assertSame($events[0]->shopName, 'Undead Legion Shop');
        $this->assertFalse($events[0]->isUpgrade);
        $this->assertFalse($events[0]->isHouseShop);
        $this->assertIsArray($events[0]->items);
    }
}
