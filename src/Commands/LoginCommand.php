<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Packet;

class LoginCommand implements CommandInterface
{
    /**
     * First command to make login in AQW server.
     * 
     * @param string $playerName A player username.
     * @param string $token You auth token
     * @return LoginCommand
     */
    public function __construct(
        private readonly string $playerName,
        private readonly string $token
    ) {}

    public function toPacket(): Packet
    {
        $packet = "<msg t='sys'>" .
            "<body action='login' r='0'>" .
            "<login z='zone_master'>" .
            "<nick><![CDATA[SPIDER#{$this->playerName}~3.01]]></nick>" .
            "<pword><![CDATA[{$this->token}]]></pword>" .
            "</login></body></msg>";

        return Packet::packetify($packet);
    }
}
