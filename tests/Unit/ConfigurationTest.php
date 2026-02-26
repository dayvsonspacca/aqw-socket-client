<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit;

use AqwSocketClient\Configuration;
use AqwSocketClient\Interpreters\AreaInterpreter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->configuration = Configuration::make();
    }

    #[Test]
    public function make_method_creates_configuration(): void
    {
        $this->assertInstanceOf(Configuration::class, $this->configuration);
    }

    #[Test]
    public function it_can_add_a_new_interpreter(): void
    {
        $this->assertEmpty($this->configuration->interpreters);

        $interpreter = new AreaInterpreter();
        $this->configuration->addInterpreter($interpreter);

        $this->assertCount(1, $this->configuration->interpreters);
        $this->assertContains($interpreter, $this->configuration->interpreters);
    }
}
