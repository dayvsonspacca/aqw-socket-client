<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Enums\Tag;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\ExperienceReward;
use AqwSocketClient\Objects\Faction;
use AqwSocketClient\Objects\GoldReward;
use AqwSocketClient\Objects\Identifiers\FactionIdentifier;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Objects\ItemReward;
use AqwSocketClient\Objects\Names\FactionName;
use AqwSocketClient\Objects\Names\QuestName;
use AqwSocketClient\Objects\Quest;
use AqwSocketClient\Objects\ClassPointsRequirement;
use AqwSocketClient\Objects\LevelRequirement;
use AqwSocketClient\Objects\Levels\PlayerLevel;
use AqwSocketClient\Objects\QuestDescription;
use AqwSocketClient\Objects\QuestTurnInItem;
use AqwSocketClient\Objects\ReputationRequirement;
use AqwSocketClient\Objects\ReputationReward;
use Override;

final class QuestLoadedEvent implements EventInterface
{
    public function __construct(
        public readonly Quest $quest,
    ) {}

    /**
     * @return ?QuestLoadedEvent
     * @mago-ignore analyzer:mixed-argument,mixed-array-access,mixed-assignment,less-specific-nested-argument-type
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if (!($message instanceof JsonMessage && $message->type === JsonMessageType::QuestsLoaded)) {
            return null;
        }

        $rawQuests = $message->data['quests'] ?? [];

        if (empty($rawQuests)) {
            return null;
        }

        $q = reset($rawQuests);
        $questId = key($rawQuests);

        $faction = new Faction(
            new FactionIdentifier((int) $q['FactionID']),
            new FactionName($q['sFaction']),
        );

        $rewards = [];
        if (($q['iExp']  ?? 0) > 0) $rewards[] = new ExperienceReward((int) $q['iExp']);
        if (($q['iGold'] ?? 0) > 0) $rewards[] = new GoldReward((int) $q['iGold']);
        if (($q['iRep']  ?? 0) > 0) $rewards[] = new ReputationReward((int) $q['iRep'], $faction);
        foreach ($q['reward'] ?? [] as $r) {
            $rewards[] = new ItemReward(
                new ItemIdentifier((int) $r['ItemID']),
                (int) $r['iRate'],
                (int) $r['iQty'],
            );
        }

        $turnInItems = [];
        foreach ($q['turnin'] ?? [] as $t) {
            $turnInItems[] = new QuestTurnInItem(
                new ItemIdentifier((int) $t['ItemID']),
                (int) $t['iQty'],
            );
        }

        $tags = [];
        if ($q['bOnce']  ?? false) $tags[] = Tag::OneTime;
        if ($q['bUpg']   ?? false) $tags[] = Tag::MemberOnly;
        if ($q['bStaff'] ?? false) $tags[] = Tag::StaffOnly;
        if ($q['bGuild'] ?? false) $tags[] = Tag::GuildQuest;

        $requirements = [];
        if (($q['iLvl']    ?? 0) > 0) $requirements[] = new LevelRequirement(new PlayerLevel((int) $q['iLvl']));
        if (($q['iReqRep'] ?? 0) > 0) $requirements[] = new ReputationRequirement((int) $q['iReqRep'], $faction);
        if (($q['iReqCP']  ?? 0) > 0) $requirements[] = new ClassPointsRequirement((int) $q['iReqCP']);

        return new self(new Quest(
            new QuestIdentifier((int) $questId),
            new QuestName($q['sName']),
            new QuestDescription($q['sDesc'], $q['sEndText']),
            $requirements,
            $rewards,
            $turnInItems,
            $tags,
        ));
    }
}
