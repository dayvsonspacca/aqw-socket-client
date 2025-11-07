<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

/**
 * Interface for classes that **listen for** and **process** specific
 * {@see AqwSocketClient\Interfaces\EventInterface} objects.
 *
 * Listeners are responsible for acting upon the interpreted server messages,
 * providing the main application logic.
 * 
 * And dont return any commands do server
 */
interface ListenerInterface
{
    /**
     * Executes logic based on the received event.
     *
     * @param EventInterface $event The interpreted event object to be processed.
     * @return void
     */
    public function listen(EventInterface $event);
}