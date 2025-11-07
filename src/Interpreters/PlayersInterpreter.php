<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\{DelimitedMessageType, JsonCommandType};
use AqwSocketClient\Events\{MovedToAreaEvent, PlayerDetectedEvent};
use AqwSocketClient\Interfaces\{InterpreterInterface, MessageInterface};
use AqwSocketClient\Messages\{DelimitedMessage, JsonCommand, JsonMessage};

/**
 * An interpreter responsible for parsing incoming server messages that are
 * related to the presence and movement of **other players** in the current area.
 *
 * It generates events for players entering the screen/area.
 */
class PlayersInterpreter implements InterpreterInterface
{
    public function __construct(
        public readonly string $username
    ) {
    }
    /**
     * Currently handles:
     * - **ExitArea** delimited messages (for player detection, based on your logic).
     * - **PlayerChange** delimited messages (for player movement/presence updates).
     *
     * Both result in a {@see AqwSocketClient\Events\PlayerDetectedEvent} with the player's name.
     *
     * @param MessageInterface $message The raw, uninterpreted message object.
     * @return array The list of {@see AqwSocketClient\Interfaces\EventInterface} objects generated from the message.
     */
    public function interpret(MessageInterface $message): array
    {
        $events = [];

        if ($message instanceof DelimitedMessage) {
            if ($message->type === DelimitedMessageType::ExitArea && count($message->data) === 2) {
                $events[] = new PlayerDetectedEvent($message->data[1]);
            }
            if ($message->type === DelimitedMessageType::PlayerChange) {
                $events[] = new PlayerDetectedEvent($message->data[0]);
            }
        } elseif ($message instanceof JsonMessage) {
            $commands = array_filter($message->commands, fn (JsonCommand $command) => $command->type === JsonCommandType::MoveToArea);
            foreach ($commands as $command) {
                if ($command->type === JsonCommandType::MoveToArea) {
                    $players = array_map(fn ($data) => $data['strUsername'], $command->data['uoBranch']);
                    $user    = array_values(array_filter($command->data['uoBranch'], fn ($player) => $player['strUsername'] === $this->username));
                    if (empty($user)) {
                        continue;
                    }

                    $events[] = new MovedToAreaEvent(
                        areaId: $command->data['areaId'],
                        players: $players,
                        userId: $user[0]['entID'],
                        mapName: $command->data['strMapName']
                    );
                }
            }
        }

        return $events;
    }
}
