<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\GameFileMetadata;
use AqwSocketClient\Objects\Identifiers\MonsterIdentifier;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use AqwSocketClient\Objects\Monster\Health;
use AqwSocketClient\Objects\Monster\Monster;
use AqwSocketClient\Objects\Names\MonsterName;
use Override;
use Psl\Dict;
use Psl\Iter;

final class MonstersDetectedEvent implements EventInterface
{
    /**
     * @param Monster[] $monsters
     */
    public function __construct(
        public readonly array $monsters,
    ) {}

    /**
     * @return ?MonstersDetectedEvent
     * @mago-ignore analyzer:mixed-argument,mixed-array-access,mixed-assignment,less-specific-nested-argument-type
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof JsonMessage && $message->type === JsonMessageType::JoinedArea) {
            $data = $message->data;

            if (!Iter\contains_key($data, 'mondef') || !Iter\contains_key($data, 'monBranch')) {
                return null;
            }
            /** @var array{mondef: array, monBranch: array} $data */

            $monBranchById = Dict\reindex($data['monBranch'], static fn($m) => $m['MonID']);

            $monsters = [];
            foreach ($data['mondef'] as $monDef) {
                $monId = (int) $monDef['MonID'];

                if (!Iter\contains_key($monBranchById, $monId)) {
                    continue;
                }

                $monBranch = $monBranchById[$monId];

                $identifier = new MonsterIdentifier($monId);
                $name = new MonsterName($monDef['strMonName']);
                $level = new MonsterLevel((int) $monDef['intLevel']);
                $health = new Health((int) $monBranch['intHPMax']);

                $fileMetadata = new GameFileMetadata($monDef['strLinkage'], $monDef['strMonFileName']);

                $monsters[] = new Monster($identifier, $name, $level, $health, $fileMetadata);
            }

            if ($monsters === []) {
                return null;
            }

            return new self($monsters);
        }

        return null;
    }
}
