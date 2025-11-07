<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

/**
 * Marks a class as an **event** received from the AQW server.
 *
 * This interface serves as a **type-hinting** marker. Implementations are
 * expected to be either:
 *
 * 1. Consumable by the {@see AqwSocketClient\Interfaces\TranslatorInterface} to generate a
 * {@see CommandInterface} if an action/response is required.
 * 2. Processed by a {@see  AqwSocketClient\Interfaces\ListenerInterface} to execute application
 * logic based on the event instead of generating a command.
 */
interface EventInterface
{
}