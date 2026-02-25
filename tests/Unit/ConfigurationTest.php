<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit;

use AqwSocketClient\Configuration;
use AqwSocketClient\Interpreters\AreaInterpreter;
use AqwSocketClient\Listeners\GlobalPlayerListener;
use AqwSocketClient\Server;
use AqwSocketClient\Translators\LoginTranslator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->configuration = Configuration::make(Server::espada());
    }

    #[Test]
    public function make_method_creates_configuration()
    {
        $this->assertInstanceOf(Configuration::class, $this->configuration);
        $this->assertInstanceOf(Server::class, $this->configuration->server);
    }

    #[Test]
    public function it_can_add_a_new_interpreter()
    {
        $this->assertEmpty($this->configuration->interpreters);

        $interpreter = new AreaInterpreter();
        $this->configuration->addInterpreter($interpreter);

        $this->assertCount(1, $this->configuration->interpreters);
        $this->assertContains($interpreter, $this->configuration->interpreters);
    }

    #[Test]
    public function it_can_add_a_new_translator()
    {
        $this->assertEmpty($this->configuration->translators);

        $translator = new LoginTranslator('Hilise', 'Not a token');
        $this->configuration->addTranslator($translator);

        $this->assertCount(1, $this->configuration->translators);
        $this->assertContains($translator, $this->configuration->translators);
    }

    #[Test]
    public function it_can_add_a_new_listener()
    {
        $this->assertEmpty($this->configuration->listeners);

        $listener = new GlobalPlayerListener();
        $this->configuration->addListener($listener);

        $this->assertCount(1, $this->configuration->listeners);
        $this->assertContains($listener, $this->configuration->listeners);
    }
}
