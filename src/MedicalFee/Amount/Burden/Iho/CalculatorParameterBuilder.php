<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho;

use DateTimeImmutable;
use DateTimeInterface;
use IjiUtils\MedicalFee\Amount\Burden\GenerationClassification;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\ElderlyIncomeClassification;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\IncomeClassification;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\IncomeClassificationAttributeMaster;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\Input as KogakuRyoyohiInput;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\KogakuCountState;
use IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased\Input as RateBasedInput;
use IjiUtils\MedicalFee\Nyugai;
use IjiUtils\MedicalFee\ValueObjects\Point;
use Psr\Log\LoggerInterface;

class CalculatorParameterBuilder
{
    public Nyugai                   $nyugai;
    public ?Point                   $point;
    public ?DateTimeInterface       $patientBirthDate;
    public ?DateTimeInterface       $standardDate;
    public ?int                     $patientAge;
    public ?IncomeClassification    $incomeClassification;
    public ?string                  $incomeClassificationKey;
    public GenerationClassification $generationClassification;
    public ?float                   $burden;
    public ?KogakuCountState        $kogakuCountState;

    private IncomeClassificationAttributeMaster $classificationMaster;
    private LoggerInterface                     $logger;

    public function __construct(
        IncomeClassificationAttributeMaster $classificationMaster,
        LoggerInterface                     $logger
    ) {
        $this->classificationMaster = $classificationMaster;
        $this->logger               = $logger;

        $this->clearState();
    }

    public function build(): Input
    {
        $this->logger->debug('start building CalculatorParameter...');

        $this->detectStandardDate();

        if ($this->incomeClassificationKey) {
            $this->incomeClassification = $this->convertIncomeClassificationFromKey(
                $this->incomeClassificationKey
            );
        }

        $this->detectIsElderly();
        $this->detectBurden();
        $this->detectKogakuIncomeClassification();

        $parameter = new Input(
            $this->standardDate,
            new RateBasedInput(
                $this->point,
                $this->burden,
            ),
            new KogakuRyoyohiInput(
                nyugai:               $this->nyugai,
                point:                $this->point,
                incomeClassification: $this->incomeClassification,
                countState:           $this->kogakuCountState,
            ),
        );

        $this->clearState();

        $this->logger->debug('calculatorParameter generated', [
            'calculator parameter' => $parameter,
        ]);

        return $parameter;
    }

    public function clearState(): void
    {
        $this->point                    = null;
        $this->burden                   = null;
        $this->nyugai                   = Nyugai::GAIRAI();
        $this->standardDate             = null;
        $this->patientBirthDate         = null;
        $this->patientAge               = null;
        $this->generationClassification = GenerationClassification::NORMAL();
        $this->incomeClassification     = null;
        $this->incomeClassificationKey  = null;
        $this->kogakuCountState         = null;
    }

    private function detectStandardDate(): void
    {
        $this->logger->debug('start detecting standard date...');

        $this->standardDate ??= new DateTimeImmutable();

        $this->logger->debug(
            'standard date detected.',
            [
                'standard date' => $this->standardDate->format('Y/m/d'),
            ]
        );
    }

    /**
     * TODO
     * - 70????????????
     * - 75????????????
     */
    private function detectIsElderly(): void
    {
        $this->logger->debug('start detecting whether patient is elderly or not...');

        $this->patientAge ??= $this->patientBirthDate
            ? $this->standardDate->diff($this->patientBirthDate)->y
            : null;

        if (!is_null($this->patientAge)) {
            $this->generationClassification = match (true) {
                $this->patientAge >= 75 => GenerationClassification::LATE_ELDERLY(),
                $this->patientAge >= 70 => GenerationClassification::EARLY_ELDERLY(),
                $this->patientAge <= 6  => GenerationClassification::PRESCHOOL(),
                default                 => GenerationClassification::NORMAL(),
            };
        }
    }

    private function detectBurden(): void
    {
        $this->logger->debug("start detecting patient's burden rate...");

        if (!is_null($this->burden)) {
            $this->logger->debug('???????????????????????????', [
                'patient burden rate' => $this->burden,
            ]);
            return;
        }

        if ($this->generationClassification->isElderly() && $this->incomeClassification?->isComparableToNonEldery()) {
            $this->logger->debug('?????????????????????????????????????????????', [
                'patient burden rate' => $this->burden,
            ]);
            $this->burden = GenerationClassification::NORMAL()->getDefaultBurdenRate();
            return;
        }

        $this->burden = $this->generationClassification->getDefaultBurdenRate();

        $this->logger->debug('?????????????????????????????????????????????', [
            'age of patient'      => $this->patientAge,
            'patient burden rate' => $this->burden,
        ]);
    }

    private function detectKogakuIncomeClassification(): void
    {
        $this->logger->debug('start detecting income classification...');

        $this->logger->debug('???????????????????????????', [
            'income classification' => $this->incomeClassificationKey,
        ]);

        if (!is_null($this->incomeClassificationKey)) {
            $this->logger->debug('????????????????????????????????????', [
                'income classification' => $this->incomeClassification,
            ]);
            return;
        }

        $this->logger->debug('???????????????????????????', [
            'income classification' => $this->incomeClassification,
        ]);

        if ($this->generationClassification->isNonElderly()) {
            return;
        }

        $this->incomeClassification ??= match ($this->burden) {
            0.3      => ElderlyIncomeClassification::UPPER_3(),
            0.2, 0.1 => ElderlyIncomeClassification::MIDDLE(),
            default  => null,
        };

        $this->logger->debug('??????????????????????????????????????????????????????', [
            'income classification' => $this->incomeClassification,
        ]);
    }

    private function convertIncomeClassificationFromKey(string $classificationKey): IncomeClassification
    {
        return $this->classificationMaster->findIncomeClassificationByKey($classificationKey);
    }
}
