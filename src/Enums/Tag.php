<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

enum Tag
{
    case MemberOnly; // bUpg
    case AcPurchasable; // bCoins
    case Temporary; // bTemp
    case QuestItem; // bQuest
    case Placeable; // bHouse
    case StaffOnly; // bStaff
    case GuildQuest; // bGuild
    case OneTime; // bOnce
}
