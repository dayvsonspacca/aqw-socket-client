<?php

declare(strict_types=1);

namespace AqwSocketClient\Enums;

enum DelimitedMessageType
{
    case Server;
    case LoginResponse;

    public static function fromString(string $string): self|false
    {
        return match ($string) {
            'loginResponse' => self::LoginResponse,
            'server'        => self::Server,
            default         => false
        };
    }
}