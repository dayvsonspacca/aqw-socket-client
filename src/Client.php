<?php

declare(strict_types=1);

namespace AqwSocketClient;

use AqwSocketClient\Commands\CommandInterface;
use AqwSocketClient\Events\EventFactoryInterface;
use AqwSocketClient\Events\EventsHandlerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\Connector;
use React\Socket\ConnectionInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class Client
{
    private ?ConnectionInterface $connection = null;
    private ?LoopInterface $loop = null;

    /**
     * @param EventFactoryInterface[] $eventFactories
     * @param EventsHandlerInterface[] $eventHandlers
     */
    public function __construct(
        private readonly Server $server,
        private readonly array $eventFactories,
        private readonly array $eventHandlers
    ) {}

    /**
     * Starts the client: initializes the event loop, connects to the server, and runs the loop.
     */
    public function run(): void
    {
        $this->loop = Loop::get();

        $this->connect($this->loop)->then(
            function (Client $client) {
            },
            function (\Throwable $e) {
                echo "Connection failed: {$e->getMessage()}\n";
            }
        );

        $this->loop->run();
    }

    /**
     * Connects to the TCP server and sets up the data listener.
     */
    private function connect(LoopInterface $loop): PromiseInterface
    {
        $connector = new Connector($loop);
        $deferred = new Deferred();

        $connector->connect("tcp://{$this->server->hostname}:{$this->server->port}")
            ->then(
                function (ConnectionInterface $connection) use ($deferred) {
                    $this->connection = $connection;
                    $connection->on('data', fn(string $data) => $this->handleIncomingData($data));
                    $deferred->resolve($this);
                },
                fn(\Throwable $e) => $deferred->reject($e)
            );

        return $deferred->promise();
    }

    /**
     * Sends a command to the server.
     */
    public function send(CommandInterface $command): void
    {
        if ($this->connection) {
            $this->connection->write($command->toPacket()->unpacketify());
        }
    }

    /**
     * Handles incoming data from the server.
     */
    private function handleIncomingData(string $message): void
    {
        $events = $this->parseEvents($message);
        $commands = $this->handleEvents($events);

        foreach ($commands as $command) {
            $this->send($command);
        }
    }

    /**
     * Parses raw message into events using registered factories.
     *
     * @return array
     */
    private function parseEvents(string $message): array
    {
        $events = [];
        foreach ($this->eventFactories as $factory) {
            $events = array_merge($events, $factory->fromMessage($message));
        }
        return $events;
    }

    /**
     * Processes events and returns commands to be sent.
     *
     * @return CommandInterface[]
     */
    private function handleEvents(array $events): array
    {
        $commands = [];
        foreach ($this->eventHandlers as $handler) {
            $commands = array_merge($commands, $handler->handle($events));
        }
        return $commands;
    }
}
