<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\EquipmentSlot;
use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\GameFileMetadata;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Item\EquippedItem;
use Override;

final class ItemEquippedEvent implements EventInterface
{
    public function __construct(
        public readonly SocketIdentifier $socketId,
        public readonly EquippedItem $item,
    ) {}

    /**
     * @return ?ItemEquippedEvent
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if (!($message instanceof JsonMessage && $message->type === JsonMessageType::EquipItem)) {
            return null;
        }

        $data = $message->data;
        $slot = EquipmentSlot::tryFrom((string) $data['strES']);

        if ($slot === null) {
            return null;
        }

        $boost = $data['sMeta'] ?? null;

        return new self(
            new SocketIdentifier((int) $data['uid']),
            new EquippedItem(
                new ItemIdentifier((int) $data['ItemID']),
                $slot,
                new GameFileMetadata((string) $data['sLink'], (string) $data['sFile']),
                $boost !== null ? (string) $boost : null,
            ),
        );
    }
}
