<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\GameFileMetadata;
use AqwSocketClient\Objects\Health;
use AqwSocketClient\Objects\Identifiers\MonsterIdentifier;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use AqwSocketClient\Objects\Monster;
use AqwSocketClient\Objects\Names\MonsterName;
use Override;

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
     * @mago-ignore analyzer:mixed-argument,mixed-array-access
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof JsonMessage && $message->type === JsonMessageType::JoinedArea) {
            /** @var array{mondef: array, monBranch: array} */
            $data = $message->data;

            $monsters = [];
            for ($i = 0; $i < count($data['monBranch']); $i++) {
                $identifier = new MonsterIdentifier((int) $data['mondef'][$i]['MonID']);
                $name = new MonsterName($data['mondef'][$i]['strMonName']);
                $level = new MonsterLevel((int) $data['mondef'][$i]['intLevel']);
                $health = new Health((int) $data['monBranch'][$i]['intHPMax']);

                $fileMetadata = new GameFileMetadata(
                    $data['mondef'][$i]['strLinkage'],
                    $data['mondef'][$i]['strMonFileName'],
                );

                $monster = new Monster($identifier, $name, $level, $health, $fileMetadata);
            }

            return new self($monsters);
        }

        return null;
    }
}
