<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Helpers;

use AqwSocketClient\Objects\AreaIdentifier;
use AqwSocketClient\Objects\SocketIdentifier;

/**
 * Pre-built valids AQW server messages.
 */
final class MessageGenerator
{
    public static function moveToArea(string $mapName, AreaIdentifier $areaIdentifier): string
    {
        return (
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"'
            . $mapName
            . '-1","uoBranch":[],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"mondef":[],"areaId":'
            . $areaIdentifier->value
            . ',"strMapName":"'
            . $mapName
            . '"}}}'
        );
    }

    public static function domainPolicy(): string
    {
        return "<cross-domain-policy><allow-access-from domain='*' to-ports='5588' /></cross-domain-policy>";
    }

    public static function loginReponded(string $username, SocketIdentifier $socketIdentifier): string
    {
        return (
            '%xt%loginResponse%-1%true%'
            . $socketIdentifier->value
            . '%'
            . $username
            . '%%2026-02-26T19:33:21%sNews=1078,sMap=news/Map-UI_r38.swf,sBook=news/spiderbook3.swf,sAssets=Assets_20251205.swf,gMenu=dynamic-gameMenu-17Jan22.swf,sVersion=R0039,QSInfo=519,iMaxBagSlots=500,iMaxBankSlots=900,iMaxHouseSlots=300,iMaxGuildMembers=800,iMaxFriends=300,iMaxLoadoutSlots=50%3.0141%'
        );
    }

    public static function exitArea(string $username): string
    {
        return '%xt%exitArea%-1%1128%' . $username . '%';
    }

    public static function moveTowards(string $username): string
    {
        return (
            '%xt%uotls%-1%'
            . $username
            . '%mvts:-1,px:500,py:375,strPad:Spawn,bResting:false,mvtd:0,tx:0,ty:0,strFrame:Enter%'
        );
    }

    public static function loadInventory(): string
    {
        return '{"t":"xt","b":{"r":-1,"o":{"bankCount":57,"cmd":"loadInventoryBig","items":[]}}}';
    }

    public static function logout(): string
    {
        return "<msg t='sys'><body action='logout' r='0'></body></msg>";
    }
}
