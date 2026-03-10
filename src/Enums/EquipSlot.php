<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

enum EquipSlot: string
{
    case Weapon = 'Weapon';
    case Helm = 'he';
    case Armor = 'ar';
    case CosmeticArmor = 'co';
    case Cape = 'ba';
    case Necklace = 'am';
    case Misc = 'mi';
    case Pet = 'pe';
    case None = 'None';
}
