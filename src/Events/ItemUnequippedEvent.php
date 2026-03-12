<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\EquipmentSlot;
use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use Override;

final class ItemUnequippedEvent implements EventInterface
{
    public function __construct(
        public readonly SocketIdentifier $socketId,
        public readonly ItemIdentifier $itemId,
        public readonly EquipmentSlot $slot,
        public readonly bool $unload,
    ) {}

    /**
     * @return ?ItemUnequippedEvent
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if (!($message instanceof JsonMessage && $message->type === JsonMessageType::UnequipItem)) {
            return null;
        }

        $data = $message->data;
        $slot = EquipmentSlot::tryFrom((string) $data['strES']);

        if ($slot === null) {
            return null;
        }

        return new self(
            new SocketIdentifier((int) $data['uid']),
            new ItemIdentifier((int) $data['ItemID']),
            $slot,
            (bool) ($data['bUnload'] ?? false),
        );
    }
}
