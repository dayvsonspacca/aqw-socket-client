<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

enum EquipmentSlot: string
{
    case Cape = 'ba';
    case Armor = 'ar';
    case Costume = 'co';
    case Weapon = 'Weapon';
    case Helm = 'he';
    case House = 'ho';
    case Pet = 'pe';
    case Accessory = 'am';
}
