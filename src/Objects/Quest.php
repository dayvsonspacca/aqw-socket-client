<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use AqwSocketClient\Enums\Tag;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Objects\Names\QuestName;
use Psl\Type;

final readonly class Quest
{
    /**
     * @param list<QuestRewardInterface> $rewards
     * @param list<QuestTurnInItem>      $turnInItems
     * @param list<Tag>                  $tags
     */
    public function __construct(
        public readonly QuestIdentifier $identifier,
        public readonly QuestName $name,
        public readonly QuestDescription $description,
        public readonly QuestRequirements $requirements,
        public readonly array $rewards,
        public readonly array $turnInItems,
        public readonly array $tags,
    ) {
        Type\vec(Type\instance_of(QuestRewardInterface::class))->assert($this->rewards);
        Type\vec(Type\instance_of(QuestTurnInItem::class))->assert($this->turnInItems);
        Type\vec(Type\instance_of(Tag::class))->assert($this->tags);
    }
}
