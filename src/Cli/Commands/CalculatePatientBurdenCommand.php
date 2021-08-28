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
        $this->addArgument('point', InputArgument::REQUIRED, 'medical fee point');

        // option "n" is reserved for "no-interaction"
        $this
            ->addOption('patient-age',            'a', InputOption::VALUE_REQUIRED, 'age of patient.')
            ->addOption('patient-birth-date',     'd', InputOption::VALUE_REQUIRED, 'birth date of patient.')
            ->addOption('patient-is-elderly',     'e', InputOption::VALUE_NONE,     'use if patient is 70 years old or over.')
            ->addOption('patient-burden-rate',    'r', InputOption::VALUE_REQUIRED, 'patient burden rate with percentage. e.g. 0.3 as 30%')
            ->addOption('kogaku-classification',  'c', InputOption::VALUE_REQUIRED, 'income classification of gendogaku ninteisho.')
            ->addOption('kogaku-is-reduced',      'R', InputOption::VALUE_NONE,     'use if gendogaku ninteisho is 多数回該当.')
            ->addOption('nyuin',                  'N', InputOption::VALUE_NONE,     'use if is in nyuin situation.')
            ->addOption('comma-separated-amount', 'C', InputOption::VALUE_NONE,     'show result burden amount formatted as comma separated format.')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $_output): int
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
        $isNyuin        = $input->getOption('nyuin') ?? false;
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

        $_output->writeln(
            $showWithCommaSeparated ? $result->getAmount() : $result->getAmount()->toInt()
        );

        return Command::SUCCESS;
    }
}
