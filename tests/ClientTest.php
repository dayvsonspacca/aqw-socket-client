<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\{Client, Configuration, Packet, Server};
use AqwSocketClient\Events\PlayerLoggedOutEvent;
use AqwSocketClient\Interfaces\{CommandInterface, EventInterface, InterpreterInterface, ListenerInterface, TranslatorInterface};
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use React\Socket\ConnectionInterface;

final class ClientTest extends TestCase
{
    /**
     * Calls a private/protected method on $object via reflection.
     */
    private function callPrivate(object $object, string $method, array $args = []): mixed
    {
        $ref = new \ReflectionMethod($object, $method);
        $ref->setAccessible(true);

        return $ref->invoke($object, ...$args);
    }

    /**
     * Reads a private/protected property from $object via reflection.
     */
    private function getPrivate(object $object, string $property): mixed
    {
        $ref = new \ReflectionProperty($object, $property);
        $ref->setAccessible(true);

        return $ref->getValue($object);
    }

    /**
     * Sets a private/protected property on $object via reflection.
     */
    private function setPrivate(object $object, string $property, mixed $value): void
    {
        $ref = new \ReflectionProperty($object, $property);
        $ref->setAccessible(true);
        $ref->setValue($object, $value);
    }

    /**
     * Builds a minimal Configuration with optional extra interpreters/translators/listeners,
     * bypassing the real AuthenticationInterpreter and LoginTranslator with mocks.
     */
    private function makeConfiguration(
        array $interpreters = [],
        array $translators = [],
        array $listeners = [],
        bool $logMessages = false
    ): Configuration {
        $config = new Configuration(
            username: 'test_user',
            password: 'test_pass',
            token: 'test_token',
            interpreters: $interpreters,
            translators: $translators,
            listeners: $listeners,
            logMessages: $logMessages,
        );

        return $config;
    }

    /**
     * Builds a Client wired with the given Configuration and an optional logger mock.
     * The real ReactPHP Connector is never touched because we never call connect().
     */
    private function makeClient(
        Configuration $configuration,
        ?LoggerInterface $logger = null
    ): Client {
        return new Client(
            server: Server::twilly(),
            configuration: $configuration,
            logger: $logger ?? $this->createStub(LoggerInterface::class),
        );
    }

    #[Test]
    public function process_buffer_does_nothing_when_no_null_terminator_present(): void
    {
        $client = $this->makeClient($this->makeConfiguration());
        $this->setPrivate($client, 'buffer', 'incomplete message without terminator');

        $this->callPrivate($client, 'processBuffer');

        $this->assertSame(
            'incomplete message without terminator',
            $this->getPrivate($client, 'buffer'),
            'Buffer must remain unchanged when no null terminator is found.'
        );
    }

    #[Test]
    public function process_buffer_consumes_single_null_terminated_message(): void
    {
        $listener = $this->createMock(ListenerInterface::class);
        $listener->expects($this->atLeastOnce())->method('listen');

        $rawJson = '{"t":"xt","b":{"r":-1,"o":{"bankCount":0,"cmd":"loadInventoryBig","items":[{"ItemID":3,"sElmt":"None","sLink":"","bExtra2":0,"bStaff":0,"iRng":10,"iDPS":0,"bCoins":0,"sES":"Weapon","bExtra1":0,"bWear":0,"sType":"Staff","EnhLvl":1,"metaValues":{},"iCost":100,"EnhPatternID":1,"iRty":13,"iQSValue":0,"iQty":1,"sReqQuests":"","iLvl":1,"sIcon":"iwstaff","iEnh":1856,"bTemp":0,"ProcID":"","CharItemID":1.073108779E9,"bPTR":0,"iHrs":769,"sFile":"items/staves/staff01.swf","iQSIndex":-1,"EnhID":1856,"EnhDPS":100,"sDesc":"Staff","iStk":1,"bBank":0,"EnhRty":1,"bEquip":1,"bHouse":0,"bUpg":0,"EnhRng":10,"sName":"Default Staff"}],"hitems":[]}}}';

        $event       = $this->createStub(EventInterface::class);
        $interpreter = $this->createMock(InterpreterInterface::class);
        $interpreter->method('interpret')->willReturn([$event]);

        $config = $this->makeConfiguration(interpreters: [$interpreter], listeners: [$listener]);
        $client = $this->makeClient($config);

        $this->setPrivate($client, 'buffer', $rawJson . "\x00");

        $this->callPrivate($client, 'processBuffer');

        $this->assertSame('', $this->getPrivate($client, 'buffer'), 'Buffer must be empty after consuming the message.');
    }

    #[Test]
    public function process_buffer_consumes_multiple_null_terminated_messages(): void
    {
        $listenCallCount = 0;

        $event       = $this->createStub(EventInterface::class);
        $interpreter = $this->createMock(InterpreterInterface::class);
        $interpreter->method('interpret')->willReturn([$event]);

        $listener = $this->createMock(ListenerInterface::class);
        $listener->method('listen')->willReturnCallback(function () use (&$listenCallCount): void {
            $listenCallCount++;
        });

        $config = $this->makeConfiguration(interpreters: [$interpreter], listeners: [$listener]);
        $client = $this->makeClient($config);

        $this->setPrivate($client, 'buffer', "%xt%server%-1%Saving Data...%\x00%xt%server%-1%Ending Session...%\x00");

        $this->callPrivate($client, 'processBuffer');

        $this->assertSame('', $this->getPrivate($client, 'buffer'));
        $this->assertSame(2, $listenCallCount);
    }

    #[Test]
    public function process_buffer_keeps_trailing_incomplete_message(): void
    {
        $config = $this->makeConfiguration();
        $client = $this->makeClient($config);

        $this->setPrivate($client, 'buffer', "complete\x00incomplete");

        $this->callPrivate($client, 'processBuffer');

        $this->assertSame('incomplete', $this->getPrivate($client, 'buffer'));
    }

    #[Test]
    public function dispatch_events_calls_all_listeners_for_each_event(): void
    {
        $eventA = $this->createStub(EventInterface::class);
        $eventB = $this->createStub(EventInterface::class);

        $listenerA = $this->createMock(ListenerInterface::class);
        $listenerA->expects($this->exactly(2))->method('listen');

        $listenerB = $this->createMock(ListenerInterface::class);
        $listenerB->expects($this->exactly(2))->method('listen');

        $config = $this->makeConfiguration(listeners: [$listenerA, $listenerB]);
        $client = $this->makeClient($config);

        $this->callPrivate($client, 'dispatchEvents', [[$eventA, $eventB]]);
    }

    #[Test]
    public function dispatch_events_does_nothing_when_event_list_is_empty(): void
    {
        $listener = $this->createMock(ListenerInterface::class);
        $listener->expects($this->never())->method('listen');

        $config = $this->makeConfiguration(listeners: [$listener]);
        $client = $this->makeClient($config);

        $this->callPrivate($client, 'dispatchEvents', [[]]);
    }

    #[Test]
    public function send_commands_writes_packet_when_translator_returns_command(): void
    {
        $event   = $this->createStub(EventInterface::class);
        $packet  = Packet::packetify('some-data');

        $command = $this->createMock(CommandInterface::class);
        $command->method('pack')->willReturn($packet);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('translate')->willReturn($command);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())->method('write')->with($packet->unpacketify());

        $config = $this->makeConfiguration(translators: [$translator]);
        $client = $this->makeClient($config);
        $this->setPrivate($client, 'connection', $connection);

        $this->callPrivate($client, 'sendCommands', [[$event]]);
    }

    #[Test]
    public function send_commands_does_not_write_when_translator_returns_null(): void
    {
        $event = $this->createStub(EventInterface::class);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('translate')->willReturn(null);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->never())->method('write');

        $config = $this->makeConfiguration(translators: [$translator]);
        $client = $this->makeClient($config);
        $this->setPrivate($client, 'connection', $connection);

        $this->callPrivate($client, 'sendCommands', [[$event]]);
    }

    #[Test]
    public function send_commands_throws_when_connection_is_null(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot send packet: connection is not open.');

        $packet  = Packet::packetify('some-data');
        $command = $this->createMock(CommandInterface::class);
        $command->method('pack')->willReturn($packet);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('translate')->willReturn($command);

        $event  = $this->createStub(EventInterface::class);
        $config = $this->makeConfiguration(translators: [$translator]);
        $client = $this->makeClient($config);

        $this->callPrivate($client, 'sendCommands', [[$event]]);
    }

    #[Test]
    public function parse_events_returns_events_from_all_interpreters(): void
    {
        $eventA = $this->createStub(EventInterface::class);
        $eventB = $this->createStub(EventInterface::class);

        $interpreterA = $this->createMock(InterpreterInterface::class);
        $interpreterA->method('interpret')->willReturn([$eventA]);

        $interpreterB = $this->createMock(InterpreterInterface::class);
        $interpreterB->method('interpret')->willReturn([$eventB]);

        $config = $this->makeConfiguration(interpreters: [$interpreterA, $interpreterB]);
        $client = $this->makeClient($config);

        $events = $this->callPrivate($client, 'parseEvents', ["%xt%server%-1%Saving Data...%\u{0000}"]);

        $this->assertContains($eventA, $events);
        $this->assertContains($eventB, $events);
    }

    #[Test]
    public function parse_events_logs_raw_message_when_log_messages_is_enabled(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('debug')
            ->with('hello-log-test');

        $config = $this->makeConfiguration(logMessages: true);
        $client = $this->makeClient($config, $logger);

        $this->callPrivate($client, 'parseEvents', ['hello-log-test']);
    }

    #[Test]
    public function parse_events_does_not_log_when_log_messages_is_disabled(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('debug');

        $config = $this->makeConfiguration(logMessages: false);
        $client = $this->makeClient($config, $logger);

        $this->callPrivate($client, 'parseEvents', ['hello-no-log']);
    }

    #[Test]
    public function process_raw_message_closes_connection_on_player_logged_out_event(): void
    {
        $logoutEvent = new PlayerLoggedOutEvent();

        $interpreter = $this->createMock(InterpreterInterface::class);
        $interpreter->method('interpret')->willReturn([$logoutEvent]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())->method('close');

        $config = $this->makeConfiguration(interpreters: [$interpreter]);
        $client = $this->makeClient($config);
        $this->setPrivate($client, 'connection', $connection);

        $this->callPrivate($client, 'processRawMessage', ["%xt%server%-1%Saving Data...%\x00"]);
    }

    #[Test]
    public function process_raw_message_does_not_close_connection_on_regular_event(): void
    {
        $regularEvent = $this->createStub(EventInterface::class);

        $interpreter = $this->createMock(InterpreterInterface::class);
        $interpreter->method('interpret')->willReturn([$regularEvent]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->never())->method('close');

        $config = $this->makeConfiguration(interpreters: [$interpreter]);
        $client = $this->makeClient($config);
        $this->setPrivate($client, 'connection', $connection);

        $this->callPrivate($client, 'processRawMessage', ["any-message\x00"]);
    }
}
