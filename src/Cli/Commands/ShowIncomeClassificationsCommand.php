<?php

declare(strict_types=1);

namespace IjiUtils\Cli\Commands;

use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\NonElderlyIncomeClassification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowIncomeClassificationsCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected static $defaultName = 'list:income-classifications';

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->addOption(
            'exclude-non-elderly',
            'N',
            InputOption::VALUE_NONE,
            'do not show income classifications for non-elderlies.',
        );
        $this->addOption(
            'exclude-elderly',
            'E',
            InputOption::VALUE_NONE,
            'do not show income classifications for elderlies.',
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $_input, OutputInterface $output)
    {
        $nonElderlyClassifications = NonElderlyIncomeClassification::values();

        $output->writeln(implode("\n", $nonElderlyClassifications));

        return Command::SUCCESS;
    }
}
