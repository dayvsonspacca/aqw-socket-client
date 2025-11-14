<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Events\{ShopLoadedEvent};
use AqwSocketClient\Interpreters\ShopInterpreter;
use AqwSocketClient\Messages\{JsonMessage};
use AqwSocketClient\Objects\Item;
use AqwSocketClient\Objects\Shop;
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

        /** @var Shop $shop */
        $shop = $events[0]->shop;

        $this->assertSame($shop->id, 216);
        $this->assertSame($shop->name, 'Undead Legion Shop');
        $this->assertFalse($shop->memberOnly);
        $this->assertSame($shop->type, Shop::ITEMS);
        $this->assertIsArray($shop->items);
        
        $this->assertCount(1, $shop->items);
        
        /** @var Item $item */
        $item = $shop->items[0];

        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame($item->id, 47482);
        $this->assertSame($item->name, 'Legion Symbiote');
        $this->assertSame($item->description, 'The parasite infecting this armor was created especially for Dage\'s Legion. Now truly be one with the Dark Lord.');
        $this->assertSame($item->type, 'Armor');
        $this->assertFalse($item->memberOnly);
        $this->assertSame($item->assetUrl, 'ULS.swf');
        $this->assertSame($item->coinType, Item::AC);
        $this->assertSame($item->coinAmount, 750);
    }
}
