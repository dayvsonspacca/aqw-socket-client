<?php

declare(strict_types=1);

namespace AqwSocketClient;

/**
 * Represents an Adventure Quest Worlds (AQW) server.
 *
 * This class provides a simple representation of a server, including its hostname, port, and name.
 * It also includes predefined factory methods for known servers.
 * @mago-ignore lint:too-many-methods
 */
final class Server
{
    /**
     * @param string $name The server's name.
     * @param string $hostname The hostname or IP address of the server.
     * @param int $port The port used to connect to the server.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $hostname,
        public readonly int $port,
    ) {}

    public static function twilly(): Server
    {
        return new self('Twilly', 'sockett4.aq.com', 5593);
    }

    public static function twig(): Server
    {
        return new self('Twig', 'sockett5.aq.com', 5589);
    }

    public static function artix(): Server
    {
        return new self('Artix', 'sockett4.aq.com', 5588);
    }

    public static function sepulchure(): Server
    {
        return new self('Sepulchure', 'sockett4.aq.com', 5591);
    }

    public static function gravelyn(): Server
    {
        return new self('Gravelyn', 'sockett5.aq.com', 5590);
    }

    public static function safiria(): Server
    {
        return new self('Safiria', 'sockett4.aq.com', 5594);
    }

    public static function sir_ver(): Server
    {
        return new self('Sir Ver', 'sockett4.aq.com', 5589);
    }

    public static function swordhaven(): Server
    {
        return new self('Swordhaven (EU)', 'euro.aqw.artix.com', 5588);
    }

    public static function galanoth(): Server
    {
        return new self('Galanoth', 'sockett5.aq.com', 5593);
    }

    public static function alteon(): Server
    {
        return new self('Alteon', 'sockett5.aq.com', 5591);
    }

    public static function yorumi(): Server
    {
        return new self('Yorumi', 'sockett5.aq.com', 5588);
    }

    public static function yokai(): Server
    {
        return new self('Yokai (SEA)', 'asia.game.artix.com', 5588);
    }

    public static function espada(): Server
    {
        return new self('Espada', 'sockett4.aq.com', 5592);
    }

    /**
     * Returns an array of all known servers.
     *
     * @return Server[]
     */
    public static function all(): array
    {
        return [
            self::twilly(),
            self::twig(),
            self::artix(),
            self::sepulchure(),
            self::gravelyn(),
            self::safiria(),
            self::sir_ver(),
            self::swordhaven(),
            self::galanoth(),
            self::alteon(),
            self::yorumi(),
            self::yokai(),
            self::espada(),
        ];
    }
}
