<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Interfaces\{InterpreterInterface, MessageInterface};
use AqwSocketClient\Messages\JsonMessage;

/**
 * Interprets messages related to the player's location and area transitions.
 */
final class AreaInterpreter implements InterpreterInterface
{
    /**
     * @param MessageInterface $message The message received from the socket client.
     * @return array An array of domain events generated from the message.
     */
    public function interpret(MessageInterface $message): array
    {
        return match ($message::class) {
            JsonMessage::class => $this->interpretJson($message),
            default => []
        };
    }

    private function interpretJson(JsonMessage $message): array
    {
        $events = [];

        if ($message->type === JsonMessageType::JoinedArea) {
            $events[] = new AreaJoinedEvent(
                $message->data['strMapName'],
                (int) explode('-', $message->data['areaName'])[1],
                (int) $message->data['areaId'],
                array_map(fn ($player) => [
                    'socket_id' => $player['entID'],
                    'name' => $player['strUsername']
                ], $message->data['uoBranch'])
            );
        }

        return $events;
    }
}
