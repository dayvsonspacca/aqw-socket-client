<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

/**
 * Defines the possible command types found within the **JSON formatted messages**
 * received from the AQW server.
 *
 * This enum maps the raw string identifier for an action to a
 * strongly-typed case for easy processing.
 */
enum JsonCommandType
{
    /**
     * Command related to a player equipping an item.
     * Maps to server string: `equipItem`.
     */
    case EquipItem;

    /**
     * Command related to a player changing the appearance of a currently equipped item.
     * Maps to server string: `wearItem`.
     */
    case WearItem;

    /**
     * Command related to the player moving between areas.
     */
    case MoveToArea;

    /**
     * Command related to the player inventory loaded.
     */
    case LoadInventoryBig;

    /**
     * Creates an enum case from the raw string identifier found within the JSON command structure.
     *
     * @param string $string The raw command type identifier (e.g., 'equipItem', 'wearItem').
     * @return self|false The corresponding enum case, or **false** if the string is unknown.
     */
    public static function fromString(string $string): self|false
    {
        return match($string) {
            'equipItem' => self::EquipItem,
            'wearItem' => self::WearItem,
            'moveToArea' => self::MoveToArea,
            'loadInventoryBig' => self::LoadInventoryBig,
            default => false
        };
    }
}
