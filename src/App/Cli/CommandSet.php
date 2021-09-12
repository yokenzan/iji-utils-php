<?php

declare(strict_types=1);

namespace IjiUtils\App\Cli;

use Ds\Vector;
use IjiUtils\App\Cli\Commands\CalculatePatientBurdenCommand;
use IjiUtils\App\Cli\Commands\CheckDigitNumberCommand;
use IjiUtils\App\Cli\Commands\ShowIncomeClassificationsCommand;
use Symfony\Component\Console\Application;

class CommandSet
{
    /**
     * @var Vector<\Symfony\Component\Console\Command\Command>
     */
    private Vector $commands;

    public function __construct(
        ShowIncomeClassificationsCommand $showIncomeClassificationsCommand,
        CalculatePatientBurdenCommand    $calculatePatientBurdenCommand,
        CheckDigitNumberCommand          $checkDigitNumberCommand
    ) {
        $this->commands = new Vector();

        $this->commands->push($showIncomeClassificationsCommand);
        $this->commands->push($calculatePatientBurdenCommand);
        $this->commands->push($checkDigitNumberCommand);
    }

    public function apply(Application $application): void
    {
        /**
         * @var \Symfony\Component\Console\Command\Command $command
         */
        foreach ($this->commands as $command) {
            $application->add($command);
        }
    }
}
