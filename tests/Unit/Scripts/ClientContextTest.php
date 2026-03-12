<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Scripts\ClientContext;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClientContextTest extends TestCase
{
    #[Test]
    public function it_returns_null_for_unknown_key(): void
    {
        $ctx = new ClientContext();
        $this->assertNull($ctx->get('missing'));
    }

    #[Test]
    public function it_stores_and_retrieves_a_value(): void
    {
        $ctx = new ClientContext();
        $ctx->set('foo', 'bar');
        $this->assertSame('bar', $ctx->get('foo'));
    }

    #[Test]
    public function it_overwrites_an_existing_value(): void
    {
        $ctx = new ClientContext();
        $ctx->set('foo', 'bar');
        $ctx->set('foo', 'baz');
        $this->assertSame('baz', $ctx->get('foo'));
    }

    #[Test]
    public function it_reports_key_presence(): void
    {
        $ctx = new ClientContext();
        $this->assertFalse($ctx->has('x'));
        $ctx->set('x', 42);
        $this->assertTrue($ctx->has('x'));
    }
}
