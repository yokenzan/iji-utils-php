<?php

declare(strict_types=1);

namespace IjiUtils\App\Cli;

use DI\Container;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class ContainerBuilder
{
    private static ?Container $container = null;

    public function build(): Container
    {
        if (is_null(self::$container)) {
            self::$container = $this->setUp();
        }

        return self::$container;
    }

    private function setUp(): Container
    {
        $container = new Container();
        $container->set(
            HandlerInterface::class,
            new StreamHandler(STDERR, Logger::ERROR)
        );
        $container->set(
            LoggerInterface::class,
            new Logger('std', [$container->get(HandlerInterface::class)])
        );

        return $container;
    }
}
