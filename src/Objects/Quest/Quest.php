<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use AqwSocketClient\Enums\Tag;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Objects\Names\QuestName;

/** @mago-ignore lint:excessive-parameter-list */
final readonly class Quest
{
    /**
     * @param list<QuestRequirementInterface> $requirements
     * @param list<QuestRewardInterface>      $rewards
     * @param list<QuestTurnInItem>           $turnInItems
     * @param list<Tag>                       $tags
     */
    public function __construct(
        public readonly QuestIdentifier $identifier,
        public readonly QuestName $name,
        public readonly QuestDescription $description,
        public readonly array $requirements,
        public readonly array $rewards,
        public readonly array $turnInItems,
        public readonly array $tags,
    ) {}
}
