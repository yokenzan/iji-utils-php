<?php

declare(strict_types=1);

namespace IjiUtils\App\Cli;

use Symfony\Component\Console\Application as ConsoleApplication;

/**
 * {@inheritDoc}
 */
class Application extends ConsoleApplication
{
    /**
     * {@inheritDoc}
     */
    public function __construct(CommandSet $commands)
    {
        parent::__construct('iji-util-php');

        $commands->apply($this);
    }
}
