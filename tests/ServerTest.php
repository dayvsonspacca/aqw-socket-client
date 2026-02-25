<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Server;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    #[Test]
    public function all_returns_array_of_all_servers(): void
    {
        $servers = Server::all();

        $this->assertIsArray($servers);
        $this->assertCount(13, $servers);

        $names = array_map(static fn(Server $s) => $s->name, $servers);

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
            'twilly' => ['Twilly', 'sockett4.aq.com', 5593],
            'twig' => ['Twig', 'sockett5.aq.com', 5589],
            'artix' => ['Artix', 'sockett4.aq.com', 5588],
            'sepulchure' => ['Sepulchure', 'sockett4.aq.com', 5591],
            'gravelyn' => ['Gravelyn', 'sockett5.aq.com', 5590],
            'safiria' => ['Safiria', 'sockett4.aq.com', 5594],
            'sir_ver' => ['Sir Ver', 'sockett4.aq.com', 5589],
            'swordhaven' => ['Swordhaven (EU)', 'euro.aqw.artix.com', 5588],
            'galanoth' => ['Galanoth', 'sockett5.aq.com', 5593],
            'alteon' => ['Alteon', 'sockett5.aq.com', 5591],
            'yorumi' => ['Yorumi', 'sockett5.aq.com', 5588],
            'yokai' => ['Yokai (SEA)', 'asia.game.artix.com', 5588],
            'espada' => ['Espada', 'sockett4.aq.com', 5592],
        ];

        foreach ($factories as $method => [$name, $host, $port]) {
            $server = Server::$method();
            $this->assertSame($name, $server->name);
            $this->assertSame($host, $server->hostname);
            $this->assertSame($port, $server->port);
        }
    }
}
