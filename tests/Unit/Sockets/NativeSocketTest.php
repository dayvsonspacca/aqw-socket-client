<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Sockets;

use AqwSocketClient\Sockets\NativeSocket;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Socket;

final class NativeSocketTest extends TestCase
{
    private NativeSocket $socket;

    protected function setUp(): void
    {
        $this->socket = new NativeSocket();
    }

    /**
     * @return array{Socket, int}
     */
    private function createServer(): array
    {
        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->assertNotFalse($server);

        socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($server, '127.0.0.1', 0);
        socket_listen($server, 1);

        $port = 0;
        $addr = null;
        socket_getsockname($server, $addr, $port);

        return [$server, $port];
    }

    private function acceptClient(Socket $server): Socket
    {
        $peer = socket_accept($server);
        $this->assertNotFalse($peer);
        return $peer;
    }

    private function sendAndWait(Socket $peer, string $data): void
    {
        socket_send($peer, $data, strlen($data), 0);

        usleep(10_000);
    }

    #[Test]
    public function it_throws_when_read_before_create(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Socket has not been created yet.');

        $this->socket->read(1024);
    }

    #[Test]
    public function it_throws_when_send_before_create(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Socket has not been created yet.');

        $this->socket->send('hello');
    }

    #[Test]
    public function it_throws_when_connect_before_create(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Socket has not been created yet.');

        $this->socket->connect('127.0.0.1', 9999);
    }

    #[Test]
    public function it_does_not_throw_when_close_before_create(): void
    {
        $this->socket->close();
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_creates_socket_successfully(): void
    {
        $this->socket->create();
        $this->socket->close();
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_when_connect_to_refused_port(): void
    {
        $this->socket->create();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to connect');

        $this->socket->connect('127.0.0.1', 1);
    }

    #[Test]
    public function it_connects_successfully_to_listening_server(): void
    {
        [$server, $port] = $this->createServer();

        $this->socket->create();
        $this->socket->connect('127.0.0.1', $port);

        $this->addToAssertionCount(1);

        $this->socket->close();
        socket_close($server);
    }

    #[Test]
    public function it_returns_number_of_bytes_sent(): void
    {
        [$server, $port] = $this->createServer();

        $this->socket->create();
        $this->socket->connect('127.0.0.1', $port);

        $peer = $this->acceptClient($server);

        $data = 'hello';
        $sent = $this->socket->send($data);

        $this->assertSame(strlen($data), $sent);

        $this->socket->close();
        socket_close($peer);
        socket_close($server);
    }

    #[Test]
    public function it_reads_correct_bytes_and_chunk_from_server(): void
    {
        [$server, $port] = $this->createServer();

        $this->socket->create();
        $this->socket->connect('127.0.0.1', $port);

        $peer = $this->acceptClient($server);

        $message = 'world';
        $this->sendAndWait($peer, $message);

        $result = $this->socket->read(1024);

        $this->assertArrayHasKey('bytes', $result);
        $this->assertArrayHasKey('chunk', $result);
        $this->assertSame(strlen($message), $result['bytes']);
        $this->assertSame($message, $result['chunk']);

        $this->socket->close();
        socket_close($peer);
        socket_close($server);
    }

    #[Test]
    public function it_returns_zero_bytes_when_no_data_available_within_timeout(): void
    {
        [$server, $port] = $this->createServer();

        $this->socket->create();
        $this->socket->connect('127.0.0.1', $port);

        $peer = $this->acceptClient($server);

        // Nothing is sent — read should exhaust retries and return 0 bytes.
        $result = $this->socket->read(1024);

        $this->assertSame(0, $result['bytes']);
        $this->assertSame('', $result['chunk']);

        $this->socket->close();
        socket_close($peer);
        socket_close($server);
    }

    #[Test]
    public function it_can_send_and_receive_round_trip(): void
    {
        [$server, $port] = $this->createServer();

        $this->socket->create();
        $this->socket->connect('127.0.0.1', $port);

        $peer = $this->acceptClient($server);

        $outgoing = 'ping';
        $this->socket->send($outgoing);

        $buf = '';
        socket_recv($peer, $buf, 1024, 0);
        $this->assertSame($outgoing, $buf);

        $reply = 'pong';
        $this->sendAndWait($peer, $reply);

        $result = $this->socket->read(1024);
        $this->assertSame($reply, $result['chunk']);

        $this->socket->close();
        socket_close($peer);
        socket_close($server);
    }

    #[Test]
    public function it_can_close_multiple_times_without_throwing(): void
    {
        [$server, $port] = $this->createServer();

        $this->socket->create();
        $this->socket->connect('127.0.0.1', $port);

        $peer = $this->acceptClient($server);

        $this->socket->close();
        $this->socket->close();

        $this->addToAssertionCount(1);

        socket_close($peer);
        socket_close($server);
    }

    #[Test]
    public function it_closes_socket_on_destruct(): void
    {
        [$server, $port] = $this->createServer();

        (static function () use ($port): void {
            $socket = new NativeSocket();
            $socket->create();
            $socket->connect('127.0.0.1', $port);
        })();

        $this->addToAssertionCount(1);

        socket_close($server);
    }
}
