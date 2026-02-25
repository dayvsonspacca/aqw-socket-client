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
    public function it_starts_disconnected(): void
    {
        $this->assertFalse($this->client->isConnected());
    }

    #[Test]
    public function it_throw_erro_when_try_disconnect_and_its_already_disconnected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not connected.');

        $this->client->disconnect();
    }
}
