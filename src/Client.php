<?php

declare(strict_types=1);

namespace AqwSocketClient;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;
use AqwSocketClient\Services\AuthService;
use GuzzleHttp\Client as GuzzleHttpClient;
use React\Promise\Deferred;
use React\Socket\{ConnectionInterface, Connector};
use RuntimeException;

class Client
{
    private ?ConnectionInterface $connection = null;

    public function __construct(
        private readonly Server $server,
        private readonly Configuration $configuration
    ) {}

    public function connect()
    {
        $connector = new Connector();
        $deferred  = new Deferred();

        $target = "tcp://{$this->server->hostname}:{$this->server->port}";
        echo "Attempting to connect to {$this->server->name} at {$target}" . PHP_EOL;

        $connector->connect($target)
            ->then(
                function (ConnectionInterface $connection) use ($deferred) {
                    echo "Connection to {$this->server->name} established." . PHP_EOL;

                    $this->connection = $connection;
                    $this->setupConnectionHandlers($connection);
                },
                fn(\Throwable $e) => $deferred->reject($e)
            );

        return $deferred->promise();
    }

    private function setupConnectionHandlers(ConnectionInterface $connection): void
    {
        $connection->on('close', function () {
            echo "Connection to {$this->server->name} closed." . PHP_EOL;
            $this->connection = null;
        });

        $connection->on('data', function (string $data) {
            $events = $this->handleMessage($data);

            $commands = [];
            foreach ($events as $event) {
                foreach ($this->configuration->translators as $translator) {
                    $commands[] = $translator->translate($event);
                }
            }

            foreach ($commands as $command) {
                $this->sendPacket($command->pack());
            }
        });
    }

    private function sendPacket(Packet $packet): void
    {
        if ($this->connection === null) {
            throw new RuntimeException("Cannot send packet, connection is not open.");
        }

        $this->connection->write($packet->unpacketify());
    }

    /**
     * @return EventInterface[]
     */
    private function handleMessage(string $data): array
    {
        $data = str_replace(["\x00"], '', $data);

        if ($this->configuration->logMessages) {
            echo $data . PHP_EOL;
        }

        $messages = [XmlMessage::fromString($data), DelimitedMessage::fromString($data)];
        $messages = array_filter($messages, fn($message) => $message);

        $events = [];
        foreach ($this->configuration->interpreters as $interpreter) {
            foreach ($messages as $message) {
                $events = array_merge($interpreter->interpret($message), $events);
            }
        }

        return $events;
    }
}
