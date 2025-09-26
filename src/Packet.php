<?php

declare(strict_types=1);

namespace AqwSocketClient;

use AqwSocketClient\Exceptions\PacketException;

/**
 * Represents a packet of data to be sent to an AQW server.
 *
 * This class handles packet creation and ensures proper formatting by appending
 * the null terminator character to the raw data. It also provides methods to
 * extract the raw packet data.
 */
class Packet
{
    /**
     * @param string $data The raw data of the packet.
     */
    private function __construct(private readonly string $data) {}

    /**
     * Creates a packet from raw string data.
     *
     * Appends the null terminator to the end of the string to conform with
     * AQW server protocol requirements.
     *
     * @param string $data The raw data to be packetified.
     * @return Packet The newly created packet.
     * @throws PacketException If the input data is empty.
     */
    public static function packetify(string $data): Packet
    {
        if (empty($data)) {
            throw PacketException::emptyPacket();
        }

        return new self($data . "\u{0000}");
    }

    /**
     * Returns the raw data of the packet.
     *
     * @return string The packet's data including the null terminator.
     */
    public function unpacketify(): string
    {
        return $this->data;
    }
}
