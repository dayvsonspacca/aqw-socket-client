<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Interpreters;

use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Events\PlayerLoggedOutEvent;
use AqwSocketClient\Interpreters\AuthenticationInterpreter;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Messages\XmlMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AuthenticationInterpreterTest extends TestCase
{
    private AuthenticationInterpreter $interpreter;

    protected function setUp(): void
    {
        $this->interpreter = new AuthenticationInterpreter();
    }

    #[Test]
    public function should_return_connection_established_event_on_cross_domain_policy(): void
    {
        $message = XmlMessage::fromString(
            '<cross-domain-policy><allow-access-from domain="*" to-ports="*"/></cross-domain-policy>',
        );
        /** @var XmlMessage $message */

        $events = $this->interpreter->interpret($message);

        $this->assertCount(1, $events);
        $this->assertInstanceOf(ConnectionEstablishedEvent::class, $events[0]);
    }

    #[Test]
    public function should_return_player_logged_out_event_on_logout_action(): void
    {
        $message = XmlMessage::fromString("<msg t='sys'><body action='logout' r='0'></body></msg>");
        /** @var XmlMessage $message */

        $events = $this->interpreter->interpret($message);

        $this->assertCount(1, $events);
        $this->assertInstanceOf(PlayerLoggedOutEvent::class, $events[0]);
    }

    #[Test]
    public function should_return_login_responded_event_on_login_response(): void
    {
        $message = DelimitedMessage::fromString(
            '%xt%loginResponse%-1%true%43472%made2903%%2025-11-07T10:38:12%sNews=995,sMap=news/Map-UI_r38.swf,sBook=news/spiderbook3.swf,sAssets=Assets_20251014.swf,gMenu=dynamic-gameMenu-17Jan22.swf,sVersion=R0038,QSInfo=519,iMaxBagSlots=500,iMaxBankSlots=900,iMaxHouseSlots=300,iMaxGuildMembers=800,iMaxFriends=300,iMaxLoadoutSlots=50%3.01%',
        );
        /** @var DelimitedMessage $message */

        $events = $this->interpreter->interpret($message);

        $this->assertCount(1, $events);
        $this->assertInstanceOf(LoginRespondedEvent::class, $events[0]);
        $this->assertTrue($events[0]->success);
        $this->assertSame(43_472, $events[0]->socketId->value);
    }

    #[Test]
    public function should_return_empty_array_for_unrelated_xml_message(): void
    {
        $message = XmlMessage::fromString('<msg t="sys"><body action="something_else" r="0"></body></msg>');
        /** @var XmlMessage $message */

        $events = $this->interpreter->interpret($message);

        $this->assertEmpty($events);
    }

    #[Test]
    public function should_return_empty_array_for_unrelated_message_type(): void
    {
        $message = JsonMessage::fromString(
            '{"t":"xt","b":{"r":-1,"o":{"bankCount":0,"cmd":"loadInventoryBig","items":[{"ItemID":3,"sElmt":"None","sLink":"","bExtra2":0,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":0,"sES":"Weapon","bExtra1":0,"bWear":0,"sType":"Staff","EnhLvl":1,"metaValues":{},"iCost":100,"EnhPatternID":1,"iRty":13,"iQSValue":0,"iQty":1,"sReqQuests":"","iLvl":1,"sIcon":"iwstaff","iEnh":1856,"bTemp":0,"ProcID":"","CharItemID":1.073108779E9,"bPTR":0,"iHrs":769,"sFile":"items/staves/staff01.swf","iQSIndex":-1,"EnhID":1856,"EnhDPS":100,"sDesc":"Staff","iStk":1,"bBank":0,"EnhRty":1,"bEquip":1,"bHouse":0,"bUpg":0,"EnhRng":10,"sName":"Default Staff"},{"ItemID":15651,"sElmt":"None","sLink":"NewHealerB2","bExtra2":0,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":1,"sES":"ar","bExtra1":0,"sType":"Class","metaValues":{"ref":17},"EnhLvl":1,"iCost":0,"EnhPatternID":1,"iRty":12,"iQSValue":0,"iQty":766,"sReqQuests":"","iLvl":1,"sIcon":"iiclass","iEnh":1959,"bTemp":0,"ProcID":"","CharItemID":1.07310878E9,"bPTR":0,"iHrs":769,"sFile":"NewHealerR2.swf","iQSIndex":-1,"EnhID":1959,"EnhDPS":100,"sDesc":"Recommended enhancement: Healer. Healers are servants of good who use their powers to aid the sick, weak, and injured. Their powerful healing magic is often the difference between a group\'s victory or doom.","iStk":1,"bBank":0,"EnhRty":1,"bEquip":1,"bHouse":0,"bUpg":0,"EnhRng":10,"sName":"Healer","sMeta":17},{"ItemID":45739,"sElmt":"None","sLink":"","bExtra2":1,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":1,"sES":"None","bExtra1":0,"metaValues":{"nosell":true},"sType":"Resource","iCost":0,"iRty":13,"iQSValue":0,"iQty":2,"sReqQuests":"","sIcon":"iibag","iLvl":1,"bTemp":0,"CharItemID":1.0731091E9,"bPTR":0,"iHrs":769,"iQSIndex":-1,"EnhID":0,"iStk":3,"sDesc":"Collect 3 of these to earn a free spin at the Wheel of Doom!","bBank":0,"bHouse":0,"bUpg":0,"bEquip":0,"sName":"Gear of Doom","sMeta":"NoSell"}],"hitems":[]}}}',
        );
        /** @var JsonMessage $message */

        $events = $this->interpreter->interpret($message);

        $this->assertEmpty($events);
    }
}
