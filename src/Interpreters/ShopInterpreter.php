<?php


declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Events\ShopLoadedEvent;
use AqwSocketClient\Interfaces\{InterpreterInterface, MessageInterface};
use AqwSocketClient\Messages\JsonMessage;

class ShopInterpreter implements InterpreterInterface
{
    public function interpret(MessageInterface $message): array
    {
        $events = [];
        if ($message instanceof JsonMessage && $message->type === JsonMessageType::ShopLoaded) {
            $events[] = new ShopLoadedEvent(
                (int) $message->data['shopinfo']['ShopID'],
                $message->data['shopinfo']['sName'],
                (bool) $message->data['shopinfo']['bUpgrd'],
                (bool) $message->data['shopinfo']['bHouse'],
                array_map(fn($item) => [
                    'id' => $item['ItemID'],
                    'name' => $item['sName'],
                    'description' => $item['sDesc']
                ], array_merge($message->data['shopinfo']['items']))
            );
        }

        return $events;
    }
}
