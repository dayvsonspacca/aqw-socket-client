<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

interface ListenerInterface
{
    public function listen(EventInterface $event);
}
