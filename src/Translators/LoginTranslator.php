<?php

declare(strict_types=1);

namespace AqwSocketClient\Translators;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\TranslatorInterface;
use AqwSocketClient\Services\AuthService;
use Throwable;

class LoginTranslator implements TranslatorInterface
{
    private readonly AuthService $authService;

    public function __construct(
        private readonly string $username,
        private readonly string $password
    ) {
        $this->authService = new AuthService();
    }

    public function translate(EventInterface $event): CommandInterface|false
    {
        return match ($event::class) {
            ConnectionEstabilishedEvent::class => (function () {
                try {
                    $token = $this->authService->getAuthToken($this->username, $this->password);
                    return new LoginCommand($this->username, $token);
                } catch (Throwable $th) {
                    echo $th->getMessage() . PHP_EOL;
                    return false;
                }
            })(),
            default => false
        };
    }
}
