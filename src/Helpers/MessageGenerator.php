<?php

declare(strict_types=1);

namespace AqwSocketClient\Helpers;

use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;

/**
 * Pre-built valids AQW server messages.
 * @mago-ignore lint:too-many-methods
 */
final class MessageGenerator
{
    public static function moveToArea(AreaName $areaName, AreaIdentifier $areaIdentifier): string
    {
        return (
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"'
            . (string) $areaName
            . '-1","uoBranch":[],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"mondef":[],"areaId":'
            . (string) $areaIdentifier
            . ',"strMapName":"'
            . (string) $areaName
            . '"}}}'
        );
    }

    public static function domainPolicy(): string
    {
        return "<cross-domain-policy><allow-access-from domain='*' to-ports='5588' /></cross-domain-policy>";
    }

    public static function loginReponded(PlayerName $playerName, SocketIdentifier $socketIdentifier): string
    {
        return (
            '%xt%loginResponse%-1%true%'
            . (string) $socketIdentifier
            . '%'
            . (string) $playerName
            . '%%2026-02-26T19:33:21%sNews=1078,sMap=news/Map-UI_r38.swf,sBook=news/spiderbook3.swf,sAssets=Assets_20251205.swf,gMenu=dynamic-gameMenu-17Jan22.swf,sVersion=R0039,QSInfo=519,iMaxBagSlots=500,iMaxBankSlots=900,iMaxHouseSlots=300,iMaxGuildMembers=800,iMaxFriends=300,iMaxLoadoutSlots=50%3.0141%'
        );
    }

    public static function exitArea(PlayerName $playerName): string
    {
        return '%xt%exitArea%-1%1128%' . (string) $playerName . '%';
    }

    public static function moveTowards(PlayerName $playerName): string
    {
        return (
            '%xt%uotls%-1%'
            . (string) $playerName
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

    public static function afk(): string
    {
        return '%xt%server%-1%Your status is now Away From Keyboard (AFK).%';
    }

    public static function alreadyInArea(): string
    {
        return '%xt%warning%-1%Cannot join a room you are already in.%';
    }

    public static function monstersDetected(): string
    {
        return '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"lair-5999","uoBranch":[],"strMapFileName":"Lair/town-Lair-29Dec24.swf","mondef":[{"sRace":"Dragonkin","MonID":"14","intLevel":25,"strLinkage":"Dragon1","strMonName":"Red Dragon","strMonFileName":"Dragon1.swf","strBehave":"walk"}],"intType":"1","monBranch":[{"intHPMax":30000,"iLvl":25,"MonMapID":14,"MonID":"14","intMP":100,"wDPS":13,"intState":1,"intMPMax":100,"bRed":"0","intHP":30000}],"sExtra":"","monmap":[],"areaId":311032,"strMapName":"lair"}}}';
    }

    public static function monstersDetectedOutOfOrder(): string
    {
        return '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"lair-5999","uoBranch":[],"strMapFileName":"Lair/town-Lair-29Dec24.swf","mondef":[{"sRace":"Dragonkin","MonID":"14","intLevel":25,"strLinkage":"Dragon1","strMonName":"Red Dragon","strMonFileName":"Dragon1.swf","strBehave":"walk"},{"sRace":"Undead","MonID":"7","intLevel":10,"strLinkage":"Zombie1","strMonName":"Zombie","strMonFileName":"Zombie1.swf","strBehave":"walk"}],"intType":"1","monBranch":[{"intHPMax":5000,"iLvl":10,"MonMapID":7,"MonID":"7","intMP":50,"wDPS":5,"intState":1,"intMPMax":50,"bRed":"0","intHP":5000},{"intHPMax":30000,"iLvl":25,"MonMapID":14,"MonID":"14","intMP":100,"wDPS":13,"intState":1,"intMPMax":100,"bRed":"0","intHP":30000}],"sExtra":"","monmap":[],"areaId":311032,"strMapName":"lair"}}}';
    }

    public static function monstersDetectedWithOrphanMondef(): string
    {
        return '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"lair-5999","uoBranch":[],"strMapFileName":"Lair/town-Lair-29Dec24.swf","mondef":[{"sRace":"Dragonkin","MonID":"14","intLevel":25,"strLinkage":"Dragon1","strMonName":"Red Dragon","strMonFileName":"Dragon1.swf","strBehave":"walk"},{"sRace":"Undead","MonID":"7","intLevel":10,"strLinkage":"Zombie1","strMonName":"Zombie","strMonFileName":"Zombie1.swf","strBehave":"walk"}],"intType":"1","monBranch":[{"intHPMax":30000,"iLvl":25,"MonMapID":14,"MonID":"14","intMP":100,"wDPS":13,"intState":1,"intMPMax":100,"bRed":"0","intHP":30000}],"sExtra":"","monmap":[],"areaId":311032,"strMapName":"lair"}}}';
    }

    public static function areaMemberOnly(): string
    {
        return '%xt%warning%-1%"ancienttrigoras" is an Membership-Only Map.%';
    }

    public static function monstersDetectedWithouMonDef(): string
    {
        return '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"lair-5999","uoBranch":[],"strMapFileName":"Lair/town-Lair-29Dec24.swf","intType":"1","monBranch":[{"intHPMax":30000,"iLvl":25,"MonMapID":14,"MonID":"14","intMP":100,"wDPS":13,"intState":1,"intMPMax":100,"bRed":"0","intHP":30000}],"sExtra":"","monmap":[],"areaId":311032,"strMapName":"lair"}}}';
    }

    public static function areaNotAvaliabel(): string
    {
        return '%xt%warning%-1%"cetoleonwar" is not available.%';
    }

    public static function areaLocked(): string
    {
        return '%xt%warning%-1%"caroling" map is locked until event begins. Get \'Portal to Frostval Event\' house item from /BaseCamp to unlock.%';
    }

    public static function questLoaded(): string
    {
        return '{"t":"xt","b":{"r":-1,"o":{"cmd":"getQuests","quests":{"868":{"QuestID":868,"sName":"Nulgath (Rare)","sDesc":"Bring me some Mana Energy from the Mana Golem.","sEndText":"AND I\'ve raised your chance of winning!","iExp":300,"iGold":13000,"iRep":300,"FactionID":4,"sFaction":"Evil","iLvl":0,"iReqRep":0,"iReqCP":0,"bOnce":0,"bUpg":0,"bStaff":0,"bGuild":0,"turnin":[{"ItemID":15385,"QuestID":868,"iQty":5}],"reward":[{"iRate":10,"ItemID":4861,"QuestID":868,"iType":1,"iQty":1}],"reqd":[]}}}}}';
    }
}
