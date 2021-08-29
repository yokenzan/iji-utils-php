<?php

declare(strict_types=1);

namespace IjiUtils\Cli\Commands;

use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\ElderlyIncomeClassification;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\NonElderlyIncomeClassification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tableHeaderRow = ['所得区分キー', '表示名称'];

        if (!$input->getOption('exclude-non-elderly')) {
            $output->writeln('70歳未満：');
            $table = new Table($output);

            foreach (NonElderlyIncomeClassification::values() as $classification) {
                list('value' => $key, 'name' => $name) = $classification->jsonSerialize();
                $table->setHeaders($tableHeaderRow);
                $table->addRow([$key, $name]);
            }

            $table->render();
        }

        if (!$input->getOption('exclude-elderly')) {
            $output->writeln('70歳以上：');
            $table = new Table($output);

            foreach (ElderlyIncomeClassification::values() as $classification) {
                list('value' => $key, 'name' => $name) = $classification->jsonSerialize();
                $table->setHeaders($tableHeaderRow);
                $table->addRow([$key, $name]);
            }

            $table->render();
        }

        return Command::SUCCESS;
    }
}
