<?php


declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Events\ShopLoadedEvent;
use AqwSocketClient\Interfaces\{EventInterface, InterpreterInterface, MessageInterface};
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\{Item, Shop};

/**
 * Interprets a raw server message (specifically a JSON message) to check if a
 * shop has been successfully loaded and generates a {@see AqwSocketClient\Events\ShopLoadedEvent}.
 *
 * This class implements the core parsing logic for shop-related data.
 */
class ShopInterpreter implements InterpreterInterface
{
    /**
     * Parses a raw server message and generates an array of event objects.
     *
     * If the message is a {@see AqwSocketClient\Messages\JsonMessage} of type {@see AqwSocketClient\Enums\JsonMessageType}::ShopLoaded,
     * a {@see AqwSocketClient\Events\ShopLoadedEvent} is created containing the shop's details.
     *
     * @param MessageInterface $message The raw message received from the socket.
     * @return EventInterface[] An array containing one or more event objects.
     */
    public function interpret(MessageInterface $message): array
    {
        $events = [];
        if ($message instanceof JsonMessage && $message->type === JsonMessageType::ShopLoaded) {
            $shop = new Shop(
                (int) $message->data['shopinfo']['ShopID'],
                $message->data['shopinfo']['sName'],
                !((bool) $message->data['shopinfo']['bHouse']) ? Shop::ITEMS : Shop::HOUSE,
                (bool) $message->data['shopinfo']['bUpgrd'],
                array_map(
                    fn ($item) => new Item(
                        (int) $item['ItemID'],
                        $item['sName'],
                        $item['sDesc'],
                        $item['sType'],
                        $item['sFile'] ?? null,
                        (bool) $item['bUpg'],
                        ((int) $item['bCoins']) ? Item::AC : Item::COINS,
                        (int) $item['iCost']
                    ),
                    array_merge($message->data['shopinfo']['items'])
                )
            );

            $events[] = new ShopLoadedEvent($shop);
        }

        return $events;
    }
}
