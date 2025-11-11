<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\JsonCommandType;
use AqwSocketClient\Events\JoinedAreaEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\{InterpreterInterface, MessageInterface};
use AqwSocketClient\Messages\JsonMessage;

class PlayerRelatedInterpreter implements InterpreterInterface
{
    public function interpret(MessageInterface $message): array
    {
        return match ($message::class) {
            JsonMessage::class => $this->interpretJson($message),
            default => []
        };
    }

    private function interpretJson(JsonMessage $message)
    {
        $events = [];
        foreach ($message->commands as $command) {
            if ($command->type === JsonCommandType::MoveToArea) {
                $events[] = new JoinedAreaEvent(
                    $command->data['strMapName'],
                    (int) explode('-', $command->data['areaName'])[1],
                    (int) $command->data['areaId'],
                    array_map(fn ($player) => $player['strUsername'], $command->data['uoBranch'])
                );
            } else if ($command->type === JsonCommandType::LoadInventoryBig) {
                $events[] = new PlayerInventoryLoadedEvent(
                    array_map(fn($item) => [
                        'name' => $item['sName'],
                        'description' => $item['sDesc'],
                        'type' => $item['sType'],
                        'file_name' => $item['sFile'] ?? null
                    ], $command->data['items'])
                );
            }
        }

        return $events;
    }
}
