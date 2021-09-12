<?php

declare(strict_types=1);

namespace Tests;

use DI\Container;
use IjiUtils\App\Cli\ContainerBuilder;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

abstract class TestCase extends FrameworkTestCase
{
    protected static ?Container $container = null;

    protected function setUpContainer(): void
    {
        if (!is_null(self::$container)) {
            return;
        }

        self::$container = (new ContainerBuilder())->build();
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpContainer();
    }
}
