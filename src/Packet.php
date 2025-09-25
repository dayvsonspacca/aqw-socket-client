<?php

declare(strict_types=1);

namespace AqwSocketClient;

use AqwSocketClient\Exceptions\PacketException;

class Packet
{
    private function __construct(private readonly string $data) {}

    /**
     * @throws PacketException
     */
    public static function packetify(string $data): Packet
    {
        if (empty($data)) {
            throw PacketException::emptyPacket();
        }

        return new self($data . "\u{0000}");
    }

    public function unpacketify(): string
    {
        return $this->data;
    }
}
