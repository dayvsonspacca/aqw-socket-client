<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

/**
 * Represents a command to log in a player to the AQW server.
 *
 * This is the first command sent to authenticate a player using their username
 * and authentication token. It constructs an XML-based packet required by the server.
 */
class LoginCommand implements CommandInterface
{
    /**
     * @param string $username The player's username.
     * @param string $token The authentication token for the player.
     */
    public function __construct(
        private readonly string $username,
        private readonly string $token
    ) {
    }

    public function pack(): Packet
    {
        return Packet::packetify(
            "<msg t='sys'>" .
                "<body action='login' r='0'>" .
                "<login z='zone_master'>" .
                "<nick><![CDATA[SPIDER#0001~{$this->username}~3.01]]></nick>" .
                "<pword><![CDATA[{$this->token}]]></pword>" .
                '</login></body></msg>'
        );
    }
}
