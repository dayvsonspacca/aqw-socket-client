<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

enum JsonCommandType
{
    case EquipItem;
    case WearItem;

    public static function fromString(string $string): self|false
    {
        return match($string) {
            'equipItem' => self::EquipItem,
            'wearItem'  => self::WearItem,
            default     => false
        };
    }
}