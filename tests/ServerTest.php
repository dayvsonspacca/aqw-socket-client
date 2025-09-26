<?php

namespace AqwSocketClient\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use AqwSocketClient\Server;

class ServerTest extends TestCase
{
    #[Test]
    public function all_returns_array_of_all_servers(): void
    {
        $servers = Server::all();

        $this->assertIsArray($servers);
        $this->assertCount(13, $servers);

        $names = array_map(fn(Server $s) => $s->name, $servers);

        $expectedNames = [
            'Twilly',
            'Twig',
            'Artix',
            'Sepulchure',
            'Gravelyn',
            'Safiria',
            'Sir Ver',
            'Swordhaven (EU)',
            'Galanoth',
            'Alteon',
            'Yorumi',
            'Yokai (SEA)',
            'Espada',
        ];

        $this->assertEquals($expectedNames, $names);
    }

    #[Test]
    public function factory_methods_return_server_instances(): void
    {
        $factories = [
            'twilly' => ['Twilly', 'socket5.aq.com', 5588],
            'twig' => ['Twig', 'socket4.aq.com', 5588],
            'artix' => ['Artix', 'socket.aq.com', 5588],
            'sepulchure' => ['Sepulchure', 'socket2.aq.com', 5590],
            'gravelyn' => ['Gravelyn', 'socket4.aq.com', 5589],
            'safiria' => ['Safiria', 'socket6.aq.com', 5588],
            'sir_ver' => ['Sir Ver', 'socket2.aq.com', 5588],
            'swordhaven' => ['Swordhaven (EU)', 'euro.aqw.artix.com', 5588],
            'galanoth' => ['Galanoth', 'socket6.aq.com', 5589],
            'alteon' => ['Alteon', 'socket4.aq.com', 5590],
            'yorumi' => ['Yorumi', 'socket3.aq.com', 5588],
            'yokai' => ['Yokai (SEA)', 'asia.game.artix.com', 5588],
            'espada' => ['Espada', 'socket2.aq.com', 5591],
        ];

        foreach ($factories as $method => [$name, $host, $port]) {
            $server = Server::$method();
            $this->assertSame($name, $server->name);
            $this->assertSame($host, $server->hostname);
            $this->assertSame($port, $server->port);
        }
    }
}
