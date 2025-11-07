<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

use AqwSocketClient\Packet;

/**
 * Marks a class as a **command** or **action** that should be sent
 * from the client back to the AQW server.
 *
 * Implementations are expected to contain all necessary data and logic
 * to be converted into a sendable packet.
 */
interface CommandInterface
{
    /**
     * Converts the command object into a ready-to-send {@see AqwSocketClient\Packet} object.
     *
     * This method handles the serialization of the command data into the
     * format required by the AQW server protocol.
     *
     * @return Packet The final packet object ready for transmission.
     */
    public function pack(): Packet;
}