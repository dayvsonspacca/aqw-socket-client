<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

/**
 * Defines the possible message types found within the **JSON formatted messages**
 * received from the AQW server.
 */
enum JsonMessageType: string
{
    /**
     * Message related to a player equipping an item.
     */
    case EquipItem = 'equipItem';

    /**
     * Message related to a player unequipping an item.
     */
    case UnequipItem = 'unequipItem';

    /**
     * Message related to a player changing the appearance of a currently equipped item.
     */
    case WearItem = 'wearItem';

    /**
     * Message related to the player joining areas.
     */
    case JoinedArea = 'moveToArea';

    /**
     * Message related to the player inventory loaded.
     */
    case InventoryLoaded = 'loadInventoryBig';

    /**
     * Message related to quests information
     */
    case QuestsLoaded = 'getQuests';
}
