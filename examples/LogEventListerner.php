<?php

use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ListenerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class LogEventListerner implements ListenerInterface
{
    private Logger $logger;

    public function __construct(string $name, string $path)
    {
        $this->logger = new Logger($name);
        $this->logger->pushHandler(new StreamHandler($path), Level::Debug);
    }

    public function listen(EventInterface $event)
    {
        $event = new ReflectionClass($event);

        $params = [];
        foreach ($event->getAttributes() as $attribute) {
            $name = $attribute->getName();
            $value = $attribute->getArguments();
            $params[$name] = $value;
        }

        $this->logger->debug('Event ' . $event->getShortName(), [
            'params' => $params
        ]);
    }
}
