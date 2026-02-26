<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Translators;

use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Objects\SocketIdentifier;
use AqwSocketClient\Translators\AuthenticationTranslator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AuthenticationTranslatorTest extends TestCase
{
    private AuthenticationTranslator $translator;

    protected function setUp(): void
    {
        $this->translator = new AuthenticationTranslator('Artirx', 'thisisnotartixpass');
    }

    #[Test]
    public function should_create_login_translator(): void
    {
        $this->assertInstanceOf(AuthenticationTranslator::class, $this->translator);
    }

    #[Test]
    public function should_translate_connection_established_to_login_command(): void
    {
        $command = $this->translator->translate(new ConnectionEstablishedEvent());

        $this->assertInstanceOf(LoginCommand::class, $command);
    }

    #[Test]
    public function should_return_null_when_dont_translate_any_event(): void
    {
        $event = new class() implements EventInterface {};
        $command = $this->translator->translate($event);
        $this->assertNull($command);
    }

    #[Test]
    public function should_translate_login_responded_to_first_login_command(): void
    {
        $command = $this->translator->translate(new LoginRespondedEvent(true, new SocketIdentifier(2)));

        $this->assertInstanceOf(JoinInitialAreaCommand::class, $command);
    }
}
