<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

enum Rarity: int
{
    case Unknown = 10;
    case Weird = 12;
    case Awesome = 13;
    case Artifact = 20;
    case Rare = 30;
    case Epic = 35;
    case Seasonal = 50;
    case Legendary = 100;
}
