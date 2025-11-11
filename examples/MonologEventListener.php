<?php

use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ListenerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use ReflectionClass;

class MonologEventListener implements ListenerInterface
{
    private Logger $logger;

    public function __construct(string $name, string $path)
    {
        $this->logger = new Logger($name);
        $this->logger->pushHandler(new StreamHandler($path), Level::Debug);
    }

    public function listen(EventInterface $event)
    {
        $class = new ReflectionClass($event);

        $params = [];
        foreach ($class->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $value = $property->getValue($event);
            $params[$name] = $value;
        }

        $this->logger->debug('Event ' . $class->getShortName(), [
            'params' => $params
        ]);
    }
}
