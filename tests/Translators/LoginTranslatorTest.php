<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Translators;

use AqwSocketClient\Commands\{FirstLoginCommand, LoginCommand};
use AqwSocketClient\Events\{ConnectionEstabilishedEvent, LoginResponseEvent};
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
    public function should_translate_connection_estabilished_to_login_command()
    {
        $command = $this->translator->translate(new ConnectionEstabilishedEvent());

        $this->assertInstanceOf(LoginCommand::class, $command);
    }

    #[Test]
    public function should_return_false_when_dont_translate_any_event()
    {
        $event   = new class () implements EventInterface {};
        $command = $this->translator->translate($event);
        $this->assertFalse($command);
    }

    #[Test]
    public function should_translate_login_response_to_first_login_command()
    {
        $command = $this->translator->translate(new LoginResponseEvent(true, 2));

        $this->assertInstanceOf(FirstLoginCommand::class, $command);
    }
}
