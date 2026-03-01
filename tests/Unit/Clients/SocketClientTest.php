<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Clients;

use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Interfaces\ClientInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;
use AqwSocketClient\Objects\Area;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\LoginScript;
use AqwSocketClient\Server;
use AqwSocketClient\Sockets\FakeSocket;
use DateTimeImmutable;
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
        $this->socket->queueResponse(MessageGenerator::domainPolicy());

        $this->client->connect();
        $messages = $this->client->receive();

        $this->assertNotEmpty($messages);
        $this->assertInstanceOf(XmlMessage::class, $messages[0]);
    }

    #[Test]
    public function it_can_receive_delimited_message_from_server(): void
    {
        $this->socket->queueResponse(MessageGenerator::loginReponded(
            new PlayerName('Hilise'),
            new SocketIdentifier(1),
        ));

        $this->client->connect();
        $messages = $this->client->receive();

        $this->assertNotEmpty($messages);
        $this->assertInstanceOf(DelimitedMessage::class, $messages[0]);
    }

    #[Test]
    public function it_receives_nothing_when_buffer_empty(): void
    {
        $this->client->connect();

        $this->assertTrue($this->client->isConnected());

        $messages = $this->client->receive();

        $this->assertEmpty($messages);
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

        $packet = new LoginCommand(new PlayerName('Hilise'), md5('test'))->pack();
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

        $packet = new LoginCommand(new PlayerName('Hilise'), md5('test'))->pack();
        $this->client->send($packet);
    }

    #[Test]
    public function it_throws_when_send_without_connection(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not connected.');

        $packet = new LoginCommand(new PlayerName('Hilise'), md5('test'))->pack();
        $this->client->send($packet);
    }

    #[Test]
    public function it_can_receive_and_send_then_receive_response(): void
    {
        $socketIdentifier = new SocketIdentifier(1);

        $this->socket->queueResponse(MessageGenerator::domainPolicy());
        $this->socket->queueResponse(MessageGenerator::loginReponded(new PlayerName('Hilise'), $socketIdentifier));

        $this->client->connect();

        $handshake = $this->client->receive();
        $this->assertInstanceOf(XmlMessage::class, $handshake[0]);

        $loginPacket = new LoginCommand(new PlayerName('Hilise'), md5(random_bytes(4)))->pack();
        $this->client->send($loginPacket);

        $messages = $this->client->receive();

        /** @var DelimitedMessage $message */
        $message = $messages[0];

        $this->assertInstanceOf(DelimitedMessage::class, $message);
        $this->assertSame($socketIdentifier->value, (int) $message->data[1]);

        $this->client->disconnect();
        $this->assertFalse($this->client->isConnected());
    }

    #[Test]
    public function it_can_run_a_script(): void
    {
        $playerName = new PlayerName('Hilise');
        $socketIdentifier = new SocketIdentifier(1);
        $token = md5('test');
        $areaIdentifier = new AreaIdentifier(1);

        $script = new LoginScript($playerName, $token);

        $this->socket->queueResponse(MessageGenerator::domainPolicy());
        $this->socket->queueResponse(MessageGenerator::loginReponded($playerName, $socketIdentifier));
        $this->socket->queueResponse(MessageGenerator::moveToArea(new AreaName('battleon'), $areaIdentifier));
        $this->socket->queueResponse(MessageGenerator::loadInventory());

        $this->client->connect();
        $result = $this->client->run($script);

        $this->assertContains(
            new LoginCommand($playerName, $token)->pack()->unpacketify(),
            $this->socket->sentData(),
            ConnectionEstablishedEvent::class . ' not received',
        );
        $this->assertContains(
            new JoinInitialAreaCommand()->pack()->unpacketify(),
            $this->socket->sentData(),
            LoginRespondedEvent::class . ' not received',
        );
        $this->assertContains(
            new LoadPlayerInventoryCommand($areaIdentifier, $socketIdentifier)->pack()->unpacketify(),
            $this->socket->sentData(),
            AreaJoinedEvent::class . ' not received',
        );

        $this->assertTrue($script->isDone());
        $this->assertSame(ScriptResult::Success, $result);
    }

    #[Test]
    public function it_disconnect_on_destruct(): void
    {
        $this->client->connect();

        $this->assertTrue($this->socket->isConnected());

        unset($this->client);

        $this->assertFalse($this->socket->isConnected());
    }

    #[Test]
    public function it_verifies_if_its_a_expirable_scrip_and_if_is_expired(): void
    {
        $playerName = new PlayerName('Hilise');
        $token = md5('test');

        $script = new LoginScript($playerName, $token);
        $this->assertFalse($script->isExpired());
        $script->expiresAt(new DateTimeImmutable('-10 seconds'));
        $this->assertTrue($script->isExpired());

        $this->client->connect();
        $result = $this->client->run($script);
        $this->client->disconnect();

        $this->assertSame(ScriptResult::Expired, $result);
    }

    #[Test]
    public function it_results_in_diconnect_if_socket_close(): void
    {
        $playerName = new PlayerName('Hilise');
        $socketIdentifier = new SocketIdentifier(1);
        $token = md5('test');
        $areaIdentifier = new AreaIdentifier(1);

        $script = new LoginScript($playerName, $token);
        $script->expiresAt(new DateTimeImmutable('-10 seconds'));

        $this->client->connect();
        $script->handle(new AreaJoinedEvent(
            new Area($areaIdentifier, new AreaName('battleon'), new RoomIdentifier(1)),
        ));
        $this->client->disconnect();
        $result = $this->client->run($script);

        $this->assertSame(ScriptResult::Disconnected, $result);
    }
}
