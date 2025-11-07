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
     */
    public function __construct(
        public readonly bool $success
    ) {
    }
}
