<?php

declare(strict_types=1);

namespace AqwSocketClient;

/**
 * Represents an Adventure Quest Worlds (AQW) server.
 *
 * This class provides a simple representation of a server, including its hostname and port.
 * It also includes predefined factory methods for known servers.
 */
class Server
{
    /**
     * @param string $hostname The hostname or IP address of the server.
     * @param int $port The port used to connect to the server.
     */
    private function __construct(
        public readonly string $hostname,
        public readonly int $port
    ) {}

    /**
     * Returns an instance representing the "Espada" AQW server.
     *
     * @return Server The Espada server instance.
     */
    public static function espada(): Server
    {
        return new self('socket2.aq.com', 5591);
    }
}
