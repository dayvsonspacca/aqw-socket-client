<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Events\JoinedAreaEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\{InterpreterInterface, MessageInterface};
use AqwSocketClient\Messages\JsonMessage;

/**
 * Interprets messages related to the player's status, inventory, and location.
 */
class PlayerRelatedInterpreter implements InterpreterInterface
{
    /**
     * @param MessageInterface $message The message received from the socket client.
     * @return array An array of domain events generated from the message (e.g., {@see AqwSocketClient\Events\JoinedAreaEvent}).
     */
    public function interpret(MessageInterface $message): array
    {
        return match ($message::class) {
            JsonMessage::class => $this->interpretJson($message),
            default => []
        };
    }

    /**
     * @param JsonMessage $message The single parsed JSON message.
     * @return array A list of generated events.
     */
    private function interpretJson(JsonMessage $message): array
    {
        $events = [];

        if ($message->type === JsonMessageType::MoveToArea) {
            $events[] = new JoinedAreaEvent(
                $message->data['strMapName'],
                (int) explode('-', $message->data['areaName'])[1],
                (int) $message->data['areaId'],
                array_map(fn ($player) => $player['strUsername'], $message->data['uoBranch'])
            );
        } else if ($message->type === JsonMessageType::LoadInventoryBig) {
            $events[] = new PlayerInventoryLoadedEvent(
                array_map(fn($item) => [
                    'name' => $item['sName'],
                    'description' => $item['sDesc'],
                    'type' => $item['sType'],
                    'file_name' => $item['sFile'] ?? null
                ], $message->data['items'])
            );
        }

        return $events;
    }
}