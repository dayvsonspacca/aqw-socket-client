<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

interface MessageInterface
{
    public static function fromString(string $message): self|false;
}