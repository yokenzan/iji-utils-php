<?php

declare(strict_types=1);

namespace IjiUtils\App\Cli\Commands;

use DateTimeImmutable;
use IjiUtils\MedicalFee\Amount\Burden\Calculator;
use IjiUtils\MedicalFee\Amount\Burden\CalculatorParameterBuilder;
use IjiUtils\MedicalFee\Amount\Burden\GenerationClassification;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\KogakuCountState;
use IjiUtils\MedicalFee\Nyugai;
use IjiUtils\MedicalFee\Point\Point;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 患者自己負担額を計算するコマンド
 */
class CalculatePatientBurdenCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected static $defaultName = 'calc:burden';

    private Calculator $calculator;

    private CalculatorParameterBuilder $parameterBuilder;

    private LoggerInterface $logger;

    public function __construct(
        Calculator                 $calculator,
        CalculatorParameterBuilder $parameterBuilder,
        LoggerInterface            $logger
    ) {
        $this->calculator       = $calculator;
        $this->parameterBuilder = $parameterBuilder;
        $this->logger           = $logger;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->addArgument('point', InputArgument::REQUIRED, '請求点数を指定します。');

        // option "n" is reserved for "no-interaction"

        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $this
            ->addOption('patient-age',              'a', InputOption::VALUE_REQUIRED, '患者年齢を指定します。')
            ->addOption('patient-birth-date',       'd', InputOption::VALUE_REQUIRED, '患者生年月日を指定します。')
            ->addOption('patient-is-early-elderly', 'e', InputOption::VALUE_NONE,     '患者が高齢受給者の場合に指定します。prior to -l and -p')
            ->addOption('patient-is-late-elderly',  'l', InputOption::VALUE_NONE,     '患者が後期高齢者の場合に指定します。prior to -p')
            ->addOption('patient-is-preschool',     'p', InputOption::VALUE_NONE,     '患者が未就学児の場合に指定します。')
            ->addOption('patient-burden-rate',      'r', InputOption::VALUE_REQUIRED, '患者定率負担割合を小数表記で指定します。')
            ->addOption('kogaku-classification',    'c', InputOption::VALUE_REQUIRED, '高額療養費・限度額認定証の所得区分を指定します。')
            ->addOption('kogaku-is-reduced',        'R', InputOption::VALUE_NONE,     '高額療養費の多数回該当である場合に指定します。')
            ->addOption('receipt-is-nyuin',         'N', InputOption::VALUE_NONE,     '入院レセプトである場合に指定します。')
            ->addOption('comma-separated-amount',   'C', InputOption::VALUE_NONE,     '計算結果をカンマ区切りで出力する場合に指定します。')
        ;
        // phpcs:enable Generic.Files.LineLength.MaxExceeded
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $patientAge      = $input->getOption('patient-age')
            ? (int)$input->getOption('patient-age')
            : null;
        $patientBirhDate = $input->getOption('patient-birth-date')
            ? new DateTimeImmutable($input->getOption('patient-birth-date'))
            : null;

        $generationClassification = match (true) {
            $input->getOption('patient-is-late-elderly')  => GenerationClassification::LATE_ELDERLY(),
            $input->getOption('patient-is-early-elderly') => GenerationClassification::EARLY_ELDERLY(),
            $input->getOption('patient-is-preschool')     => GenerationClassification::PRESCHOOL(),
            default                                       => GenerationClassification::NORMAL(),
        };

        $point      = Point::generate((int)$input->getArgument('point'));
        $burdenRate = !is_null($input->getOption('patient-burden-rate'))
            ? (float)$input->getOption('patient-burden-rate')
            : null;

        $classificationKey = $input->getOption('kogaku-classification');
        $nyugai            = $input->getOption('receipt-is-nyuin') ? Nyugai::NYUIN() : Nyugai::GAIRAI();
        $kogakuCountState  = $input->getOption('kogaku-is-reduced')
            ? KogakuCountState::REDUCED()
            : KogakuCountState::NORMAL();

        $showWithCommaSeparated = $input->getOption('comma-separated-amount');

        if ($kogakuCountState->isReduced() && is_null($classificationKey)) {
            throw new InvalidArgumentException();
        }

        $this->parameterBuilder->patientAge               = $patientAge;
        $this->parameterBuilder->patientBirthDate         = $patientBirhDate;
        $this->parameterBuilder->burden                   = $burdenRate;
        $this->parameterBuilder->kogakuCountState         = $kogakuCountState;
        $this->parameterBuilder->point                    = $point;
        $this->parameterBuilder->nyugai                   = $nyugai;
        $this->parameterBuilder->generationClassification = $generationClassification;
        $this->parameterBuilder->incomeClassificationKey  = $classificationKey;

        $result = $this->calculator->calculate($parameter = $this->parameterBuilder->build());

        $this->logger->debug('calculation information', ['parameter' => $parameter]);
        $this->logger->debug('calculation information', ['result'    => $result]);

        $output->writeln(
            $showWithCommaSeparated
                ? (string)$result->getAmount()
                : (string)$result->getAmount()->toInt()
        );

        return Command::SUCCESS;
    }
}
