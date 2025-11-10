<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

/**
 * Represents the **response** received from the server after a client
 * attempts a login operation.
 *
 * This event signals whether the authentication was successful or failed.
 */
class LoginResponseEvent implements EventInterface
{
    /**
     * @param bool $success Indicates whether the login attempt was successful (**true**)
     * or failed (**false**).
     * @param ?int $socketId The **socket ID** for the current connection. This is a **temporary** identifier assigned by the server, distinct from the permanent user account ID. It changes with every new connection.
     */
    public function __construct(
        public readonly bool $success,
        public readonly ?int $socketId
    ) {
    }
}
