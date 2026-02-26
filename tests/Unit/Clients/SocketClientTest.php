<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Clients;

use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Interfaces\ClientInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;
use AqwSocketClient\Server;
use AqwSocketClient\Tests\Fakes\FakeSocket;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class SocketClientTest extends TestCase
{
    private FakeSocket $socket;
    private ClientInterface $client;

    protected function setUp(): void
    {
        $this->socket = new FakeSocket();
        $this->client = new SocketClient(Server::espada(), $this->socket);
    }

    #[Test]
    public function it_creates_client_with_config(): void
    {
        $this->assertInstanceOf(SocketClient::class, $this->client);
    }

    #[Test]
    public function it_starts_disconnected(): void
    {
        $this->assertFalse($this->client->isConnected());
    }

    #[Test]
    public function it_connects_successfully(): void
    {
        $this->assertFalse($this->client->isConnected());

        $this->client->connect();

        $this->assertTrue($this->client->isConnected());
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
    public function it_throws_when_fail_to_connect_with_server(): void
    {
        $this->socket->failOnConnect();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Failed to connect:/');

        $this->client->connect();
    }

    #[Test]
    public function it_disconnects_successfully(): void
    {
        $this->client->connect();
        $this->client->disconnect();

        $this->assertFalse($this->client->isConnected());
    }

    #[Test]
    public function it_throws_when_disconnect_without_connection(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not connected.');

        $this->client->disconnect();
    }

    #[Test]
    public function it_can_receive_xml_message_from_server(): void
    {
        $this->socket->queueResponse('<msg t="sys"><body action="onConnect" r="0" /></msg>');

        $this->client->connect();
        $messages = $this->client->receive();

        $this->assertNotEmpty($messages);
        $this->assertInstanceOf(XmlMessage::class, $messages[0]);
    }

    #[Test]
    public function it_can_receive_delimited_message_from_server(): void
    {
        $this->socket->queueResponse('%xt%loginResponse%-1%0%');

        $this->client->connect();
        $messages = $this->client->receive();

        $this->assertNotEmpty($messages);
        $this->assertInstanceOf(DelimitedMessage::class, $messages[0]);
    }

    #[Test]
    public function it_disconnects_when_server_closes_connection(): void
    {
        $this->client->connect();

        $this->assertTrue($this->client->isConnected());
        
        $this->client->receive();

        $this->assertFalse($this->client->isConnected());
    }

    #[Test]
    public function it_throws_when_receive_fails(): void
    {
        $this->socket->failOnRead();
        $this->client->connect();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Failed to receive data:/');

        $this->client->receive();
    }

    #[Test]
    public function it_throws_when_receive_without_connection(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not connected.');

        $this->client->receive();
    }

    #[Test]
    public function it_can_send_packets_to_server(): void
    {
        $this->client->connect();

        $packet = (new LoginCommand('PlayerOne', md5('test')))->pack();
        $this->client->send($packet);

        $this->assertNotEmpty($this->socket->sentData());
        $this->assertSame($packet->unpacketify(), $this->socket->lastSent());
    }

    #[Test]
    public function it_throws_when_send_fails(): void
    {
        $this->socket->failOnSend();
        $this->client->connect();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Failed to send data:/');

        $packet = (new LoginCommand('PlayerOne', md5('test')))->pack();
        $this->client->send($packet);
    }

    #[Test]
    public function it_throws_when_send_without_connection(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not connected.');

        $packet = (new LoginCommand('PlayerOne', md5('test')))->pack();
        $this->client->send($packet);
    }

    #[Test]
    public function it_can_receive_and_send_then_receive_response(): void
    {
        $this->socket->queueResponse('<msg t="sys"><body action="onConnect" r="0" /></msg>');
        $this->socket->queueResponse('%xt%loginResponse%-1%0%Character data could not be retrieved. Please Login and try again.%');

        $this->client->connect();

        $handshake = $this->client->receive();
        $this->assertInstanceOf(XmlMessage::class, $handshake[0]);

        $loginPacket = (new LoginCommand('PlayerOne', md5(random_bytes(4))))->pack();
        $this->client->send($loginPacket);

        $messages = $this->client->receive();

        /** @var DelimitedMessage $message */
        $message = $messages[0];

        $this->assertInstanceOf(DelimitedMessage::class, $message);
        $this->assertSame(
            'Character data could not be retrieved. Please Login and try again.',
            $message->data[1],
        );

        $this->client->disconnect();
        $this->assertFalse($this->client->isConnected());
    }
}
