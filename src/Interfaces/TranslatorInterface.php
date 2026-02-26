<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

/**
 * Converts an incoming {@see EventInterface} into an outgoing {@see CommandInterface}.
 *
 * Translators are designed for **automatic, protocol-level reactions** to server events —
 * responses that should always happen regardless of script context, such as replying
 * to a connection handshake or acknowledging a login response.
 *
 * For logic that depends on script state or specific conditions,
 * use {@see AqwSocketClient\Interfaces\ScriptInterface::handle()} instead.
 */
interface TranslatorInterface
{
    /**
     * Translates a specific event into a command object that can be
     * processed and sent to the AQW server.
     *
     * @param EventInterface $event The incoming event to be translated.
     * @return CommandInterface|null The command object ready to be sent, or **null**
     * if the event does not require a response/command.
     */
    public function translate(EventInterface $event): ?CommandInterface;
}
