<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\EquipmentSlot;
use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use Override;

final class PlayerPreferencesLoadedEvent implements EventInterface
{
    /**
     * @param array<string, ItemIdentifier> $costumes Keyed by EquipmentSlot value
     */
    public function __construct(
        public readonly array $costumes,
    ) {}

    /**
     * @return ?PlayerPreferencesLoadedEvent
     * @mago-ignore analyzer:invalid-iterator
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if (!($message instanceof JsonMessage && $message->type === JsonMessageType::PreferencesLoaded)) {
            return null;
        }

        $raw = $message->data['result']['costumes'] ?? [];
        $costumes = [];

        foreach ($raw as $slotValue => $itemId) {
            $slot = EquipmentSlot::tryFrom((string) $slotValue);

            if ($slot === null) {
                continue;
            }

            $costumes[$slot->value] = new ItemIdentifier((int) $itemId);
        }

        return new self($costumes);
    }
}
