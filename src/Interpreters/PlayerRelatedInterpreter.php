<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\JsonCommandType;
use AqwSocketClient\Events\JoinedAreaEvent;
use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\MessageInterface;
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
                    (int) $command->data['areaId']
                );
            }
        }

        return $events;
    }
}
