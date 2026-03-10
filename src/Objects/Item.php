<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use AqwSocketClient\Enums\EquipSlot;
use AqwSocketClient\Enums\ItemType;
use AqwSocketClient\Enums\Rarity;
use AqwSocketClient\Enums\Tag;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\Names\ItemName;
use Psl\Type;

final readonly class Item
{
    /**
     * @param list<Tag> $tags
     */
    public function __construct(
        public readonly ItemIdentifier $identifier,
        public readonly ItemName $name,
        public readonly ItemType $type,
        public readonly EquipSlot $equipSlot,
        public readonly ?Rarity $rarity,
        public readonly int $maxStack,
        public readonly ?GameFileMetadata $file,
        public readonly array $tags,
    ) {
        Type\positive_int()->assert($this->maxStack);
        Type\vec(Type\instance_of(Tag::class))->assert($this->tags);
    }
}
