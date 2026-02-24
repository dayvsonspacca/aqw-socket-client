<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Translators;

use AqwSocketClient\Commands\{JoinInitialAreaCommand, LoginCommand};
use AqwSocketClient\Events\{ConnectionEstablishedEvent, LoginRespondedEvent};
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Translators\LoginTranslator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginTranslatorTest extends TestCase
{
    private readonly LoginTranslator $translator;

    protected function setUp(): void
    {
        $this->translator = new LoginTranslator('Artirx', 'thisisnotartixpass');
    }

    #[Test]
    public function should_create_login_translator()
    {
        $this->assertInstanceOf(LoginTranslator::class, $this->translator);
    }

    #[Test]
    public function should_translate_connection_established_to_login_command()
    {
        $command = $this->translator->translate(new ConnectionEstablishedEvent());

        $this->assertInstanceOf(LoginCommand::class, $command);
    }

    #[Test]
    public function should_return_null_when_dont_translate_any_event()
    {
        $event   = new class () implements EventInterface {};
        $command = $this->translator->translate($event);
        $this->assertNull($command);
    }

    #[Test]
    public function should_translate_login_responded_to_first_login_command()
    {
        $command = $this->translator->translate(new LoginRespondedEvent(true, 2));

        $this->assertInstanceOf(JoinInitialAreaCommand::class, $command);
    }
}
