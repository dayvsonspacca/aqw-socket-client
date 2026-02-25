<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Integration\Clients;

use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Configuration;
use AqwSocketClient\Interfaces\ClientInterface;
use AqwSocketClient\Server;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class SocketClientTest extends TestCase
{
    private ClientInterface $client;
    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->configuration = Configuration::make(Server::espada());
        $this->client = new SocketClient($this->configuration);
    }

    #[Test]
    public function it_creates_client_with_config()
    {
        $this->assertInstanceOf(SocketClient::class, $this->client);
        $this->assertSame($this->client->configuration, $this->configuration);
    }

    #[Test]
    public function it_connects_successfully(): void
    {
        $this->assertFalse($this->client->isConnected());

        $this->client->connect();

        $this->assertTrue($this->client->isConnected());
    }

    #[Test]
    public function it_disconnects_successfully(): void
    {
        $this->client->connect();
        $this->client->disconnect();

        $this->assertFalse($this->client->isConnected());
    }

    #[Test]
    public function it_starts_disconnected(): void
    {
        $this->assertFalse($this->client->isConnected());
    }

    #[Test]
    public function it_throws_when_already_connected(): void
    {
        $this->client->connect();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Already connected.');

        $this->client->connect();
    }

    #[Test]
    public function it_throws_when_disconnect_without_connection(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not connected.');

        $this->client->disconnect();
    }

    #[Test]
    public function it_throws_when_fail_to_connect_with_server(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Failed to connect:/');

        $client = new SocketClient(Configuration::make(new Server('Fake Server', '127.0.0.1', 0)));
        $client->connect();
    }
}
