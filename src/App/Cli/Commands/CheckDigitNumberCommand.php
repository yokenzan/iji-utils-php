<?php

declare(strict_types=1);

namespace IjiUtils\App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDigitNumberCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected static $defaultName = 'check:digit-number';

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->addArgument('number');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (str_split($input->getArgument('number')) as $digit) {
            $output->writeln($digit);
        }

        return Command::SUCCESS;
    }
}
