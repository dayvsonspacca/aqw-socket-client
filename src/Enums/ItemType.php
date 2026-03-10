<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

enum ItemType: string
{
    case Sword = 'Sword';
    case Dagger = 'Dagger';
    case Axe = 'Axe';
    case Staff = 'Staff';
    case Gun = 'Gun';
    case Mace = 'Mace';
    case Polearm = 'Polearm';
    case Gauntlet = 'Gauntlet';
    case Helm = 'Helm';
    case Armor = 'Armor';
    case Cape = 'Cape';
    case Necklace = 'Necklace';
    case CharacterClass = 'Class';
    case Pet = 'Pet';
    case House = 'House';
    case FloorItem = 'Floor Item';
    case WallItem = 'Wall Item';
    case Misc = 'Misc';
    case Resource = 'Resource';
    case Item = 'Item';
    case QuestItem = 'Quest Item';
}
