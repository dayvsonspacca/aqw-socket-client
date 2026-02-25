<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;

/**
 * Interprets messages related to the player's data, such as inventory.
 */
final class PlayerInterpreter implements InterpreterInterface
{
    /**
     * @param MessageInterface $message The message received from the socket client.
     * @return array An array of domain events generated from the message.
     */
    public function interpret(MessageInterface $message): array
    {
        return match ($message::class) {
            JsonMessage::class => $this->interpretJson($message),
            default => [],
        };
    }

    private function interpretJson(JsonMessage $message): array
    {
        $events = [];

        if ($message->type === JsonMessageType::InventoryLoaded) {
            $events[] = new PlayerInventoryLoadedEvent(array_map(static fn($item) => [
                'name' => $item['sName'],
                'description' => $item['sDesc'],
                'type' => $item['sType'],
                'file_name' => $item['sFile'] ?? null,
            ], $message->data['items']));
        }

        return $events;
    }
}
