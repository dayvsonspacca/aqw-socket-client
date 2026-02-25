<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Messages\JsonMessage;

final class PlayerInventoryLoadedEvent implements EventInterface
{
    /**
     * @param array<int, array{
     *     name: string,
     *     description: string,
     *     type: string,
     *     file_name: string|null
     * }> $items
     */
    public function __construct(
        public readonly array $items,
    ) {}

    public static function fromJsonMessage(JsonMessage $message): ?self
    {
        if ($message->type !== JsonMessageType::InventoryLoaded) {
            return null;
        }

        /**
         * @var array{
         *     items: array<int, array{
         *         sName: string,
         *         sDesc: string,
         *         sType: string,
         *         sFile?: string
         *     }>
         * } $data
         */
        $data = $message->data;

        $items = [];

        foreach ($data['items'] as $item) {
            $items[] = [
                'name' => $item['sName'],
                'description' => $item['sDesc'],
                'type' => $item['sType'],
                'file_name' => $item['sFile'] ?? null,
            ];
        }

        return new self($items);
    }
}
