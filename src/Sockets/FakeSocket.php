<?php

declare(strict_types=1);

namespace AqwSocketClient\Sockets;

use AqwSocketClient\Interfaces\SocketInterface;
use RuntimeException;

/**
 * Fake in-memory socket for unit tests.
 */
final class FakeSocket implements SocketInterface
{
    private bool $connected = false;

    /** @var string */
    private string $buffer = '';

    /** @var string[] */
    private array $sentData = [];

    private bool $shouldFailOnConnect = false;
    private bool $shouldFailOnRead = false;
    private bool $shouldFailOnSend = false;

    public function queueResponse(string $message): self
    {
        $this->buffer .= $message . "\u{0000}";

        return $this;
    }

    public function failOnConnect(): self
    {
        $this->shouldFailOnConnect = true;

        return $this;
    }

    public function failOnRead(): self
    {
        $this->shouldFailOnRead = true;

        return $this;
    }

    public function failOnSend(): self
    {
        $this->shouldFailOnSend = true;

        return $this;
    }

    public function isConnected(): bool
    {
        return $this->connected;
    }

    /**
     * @return array<string>
     */
    public function sentData(): array
    {
        return $this->sentData;
    }

    public function lastSent(): ?string
    {
        return $this->sentData !== [] ? end($this->sentData) : null;
    }

    #[\Override]
    public function create(): void {}

    #[\Override]
    public function connect(string $hostname, int $port): void
    {
        if ($this->shouldFailOnConnect) {
            throw new RuntimeException('Failed to connect: Connection refused');
        }

        $this->connected = true;
    }

    #[\Override]
    public function close(): void
    {
        $this->connected = false;
    }

    #[\Override]
    public function read(int $length): array
    {
        if ($this->shouldFailOnRead) {
            throw new RuntimeException('Failed to receive data: Socket error');
        }

        if ($this->buffer === '') {
            return ['bytes' => 0, 'chunk' => ''];
        }

        $chunk = substr($this->buffer, 0, $length);
        $this->buffer = substr($this->buffer, $length);

        return ['bytes' => strlen($chunk), 'chunk' => $chunk];
    }

    #[\Override]
    public function send(string $data): int
    {
        if ($this->shouldFailOnSend) {
            throw new RuntimeException('Failed to send data: Socket error');
        }

        $this->sentData[] = $data;

        return strlen($data);
    }
}
