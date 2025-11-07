<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Configuration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    #[Test]
    public function should_create_configuratio()
    {
        $configuration = new Configuration(
            username: 'Artix',
            password: 'this is not artix password',
            token: 'thisisnotartixtoken',
            logMessages: true,
            listeners: []
        );

        $this->assertTrue($configuration->logMessages);
        $this->assertSame($configuration->username, 'Artix');
        $this->assertSame($configuration->password, 'this is not artix password');
        $this->assertSame($configuration->token, 'thisisnotartixtoken');
        $this->assertSame($configuration->listeners, []);
    }
}
