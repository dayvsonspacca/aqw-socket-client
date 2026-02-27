<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Clients;

use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Interfaces\ClientInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;
use AqwSocketClient\Objects\AreaIdentifier;
use AqwSocketClient\Objects\SocketIdentifier;
use AqwSocketClient\Scripts\LoginScript;
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

        $packet = new LoginCommand('PlayerOne', md5('test'))->pack();
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

        $packet = new LoginCommand('PlayerOne', md5('test'))->pack();
        $this->client->send($packet);
    }

    #[Test]
    public function it_throws_when_send_without_connection(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not connected.');

        $packet = new LoginCommand('PlayerOne', md5('test'))->pack();
        $this->client->send($packet);
    }

    #[Test]
    public function it_can_receive_and_send_then_receive_response(): void
    {
        $this->socket->queueResponse('<msg t="sys"><body action="onConnect" r="0" /></msg>');
        $this->socket->queueResponse(
            '%xt%loginResponse%-1%0%Character data could not be retrieved. Please Login and try again.%',
        );

        $this->client->connect();

        $handshake = $this->client->receive();
        $this->assertInstanceOf(XmlMessage::class, $handshake[0]);

        $loginPacket = new LoginCommand('PlayerOne', md5(random_bytes(4)))->pack();
        $this->client->send($loginPacket);

        $messages = $this->client->receive();

        /** @var DelimitedMessage $message */
        $message = $messages[0];

        $this->assertInstanceOf(DelimitedMessage::class, $message);
        $this->assertSame('Character data could not be retrieved. Please Login and try again.', $message->data[1]);

        $this->client->disconnect();
        $this->assertFalse($this->client->isConnected());
    }

    #[Test]
    public function it_can_run_a_script(): void
    {
        $script = new LoginScript('Hilise', md5('test'));

        $this->socket->queueResponse(
            "<cross-domain-policy><allow-access-from domain='*' to-ports='5588' /></cross-domain-policy>",
        );
        $this->socket->queueResponse(
            '%xt%loginResponse%-1%true%1%Hilise%%2026-02-26T19:33:21%sNews=1078,sMap=news/Map-UI_r38.swf,sBook=news/spiderbook3.swf,sAssets=Assets_20251205.swf,gMenu=dynamic-gameMenu-17Jan22.swf,sVersion=R0039,QSInfo=519,iMaxBagSlots=500,iMaxBankSlots=900,iMaxHouseSlots=300,iMaxGuildMembers=800,iMaxFriends=300,iMaxLoadoutSlots=50%3.0141%',
        );
        $this->socket->queueResponse(
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"battleon-1","uoBranch":[{"entID":1,"strUsername":"Hilise"}],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"mondef":[],"areaId":1,"strMapName":"battleon"}}}',
        );
        $this->socket->queueResponse(
            '{"t":"xt","b":{"r":-1,"o":{"bankCount":57,"cmd":"loadInventoryBig","items":[]}}}',
        );

        $this->client->connect();
        $this->client->run($script);

        $this->assertContains(
            new LoginCommand('Hilise', md5('test'))->pack()->unpacketify(),
            $this->socket->sentData(),
            ConnectionEstablishedEvent::class . ' not received',
        );
        $this->assertContains(
            new JoinInitialAreaCommand()->pack()->unpacketify(),
            $this->socket->sentData(),
            LoginRespondedEvent::class . ' not received',
        );
        $this->assertContains(
            new LoadPlayerInventoryCommand(new AreaIdentifier(1), new SocketIdentifier(1))->pack()->unpacketify(),
            $this->socket->sentData(),
            AreaJoinedEvent::class . ' not received',
        );

        $this->assertTrue($script->isDone());
    }
}
