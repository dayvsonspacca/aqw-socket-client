<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

interface TranslatorInterface
{
    public function translate(EventInterface $event): CommandInterface|false;
}