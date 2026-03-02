<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

enum ScriptResult
{
    case Success;
    case Expired;
    case Disconnected;
    case Failed;
}
