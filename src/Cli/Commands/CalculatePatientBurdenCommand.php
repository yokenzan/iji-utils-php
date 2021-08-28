<?php

declare(strict_types=1);

namespace IjiUtils\Cli\Commands;

use DateTimeImmutable;
use IjiUtils\MedicalFee\Amount\Burden\Calculator;
use IjiUtils\MedicalFee\Amount\Burden\CalculatorParameterBuilder;
use IjiUtils\MedicalFee\Point\Point;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CalculatePatientBurdenCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected static $defaultName = 'calc:burden';

    private Calculator $calculator;

    private CalculatorParameterBuilder $parameterBuilder;

    public function __construct(
        Calculator                 $calculator,
        CalculatorParameterBuilder $parameterBuilder
    ) {
        $this->calculator       = $calculator;
        $this->parameterBuilder = $parameterBuilder;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->addArgument('point', InputArgument::REQUIRED, '請求点数を指定します。');

        // option "n" is reserved for "no-interaction"

        $this
            ->addOption('patient-age',            'a', InputOption::VALUE_REQUIRED, '患者年齢を指定します。')
            ->addOption('patient-birth-date',     'd', InputOption::VALUE_REQUIRED, '患者生年月日を指定します。')
            ->addOption('patient-is-elderly',     'e', InputOption::VALUE_NONE,     '患者が70歳以上の場合に指定します。.')
            ->addOption('patient-burden-rate',    'r', InputOption::VALUE_REQUIRED, '患者定率負担割合を小数表記で指定します。')
            ->addOption('kogaku-classification',  'c', InputOption::VALUE_REQUIRED, '高額療養費・限度額認定証の所得区分を指定します。')
            ->addOption('kogaku-is-reduced',      'R', InputOption::VALUE_NONE,     '高額療養費の多数回該当である場合に指定します。')
            ->addOption('nyuin',                  'N', InputOption::VALUE_NONE,     '入院レセプトである場合に指定します。')
            ->addOption('comma-separated-amount', 'C', InputOption::VALUE_NONE,     '計算結果をカンマ区切りで出力する場合に指定します。')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * parse given parametors
         */
        $patientAge       = $input->getOption('patient-age')
            ? (int)$input->getOption('patient-age')
            : null;
        $patientBirhDate  = $input->getOption('patient-birth-date')
            ? new DateTimeImmutable($input->getOption('patient-birth-date'))
            : null;
        $patientIsElderly = $input->getOption('patient-is-elderly') ?: null;

        $point      = Point::generate((int)$input->getArgument('point'));
        $burdenRate = !is_null($input->getOption('patient-burden-rate'))
            ? (float)$input->getOption('patient-burden-rate')
            : null;

        $classification = $input->getOption('kogaku-classification');
        $isNyuin        = $input->getOption('nyuin');
        $isReduced      = $input->getOption('kogaku-is-reduced');

        $showWithCommaSeparated = $input->getOption('comma-separated-amount');

        if ($isReduced && is_null($classification)) {
            throw new InvalidArgumentException();
        }

        $this->parameterBuilder->isElderly            = $patientIsElderly;
        $this->parameterBuilder->patientAge           = $patientAge;
        $this->parameterBuilder->patientBirthDate     = $patientBirhDate;
        $this->parameterBuilder->burden               = $burdenRate;
        $this->parameterBuilder->isReduced            = $isReduced;
        $this->parameterBuilder->point                = $point;
        $this->parameterBuilder->isNyuin              = $isNyuin;
        $this->parameterBuilder->incomeClassification = $classification;

        $result = $this->calculator->calculate($this->parameterBuilder->build());

        $output->writeln(
            $showWithCommaSeparated
            ? (string)$result->getAmount()
            : (string)$result->getAmount()->toInt()
        );

        return Command::SUCCESS;
    }
}
