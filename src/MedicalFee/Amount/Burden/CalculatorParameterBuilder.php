<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden;

use DateTimeImmutable;
use DateTimeInterface;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\CalculatorParameter as KogakuRyoyohiCalculatorParameter;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\ElderlyIncomeClassification;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\IncomeClassification;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\IncomeClassificationAttributeMaster;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\KogakuCountState;
use IjiUtils\MedicalFee\Amount\Burden\RateBased\CalculatorParameter as RateBasedCalculatorParameter;
use IjiUtils\MedicalFee\Nyugai;
use IjiUtils\MedicalFee\Point\Point;
use Psr\Log\LoggerInterface;

class CalculatorParameterBuilder
{
    public const DEFAULT_PATIENT_BURDEN_RATE = 0.3;

    public Nyugai                   $nyugai;
    public ?Point                   $point;
    public ?DateTimeInterface       $patientBirthDate;
    public ?DateTimeInterface       $standardDate;
    public ?int                     $patientAge;
    public ?IncomeClassification    $incomeClassification;
    public ?string                  $incomeClassificationKey;
    public GenerationClassification $generationClassification;
    public ?float                   $burden;
    public KogakuCountState         $kogakuCountState;

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

    public function build(): CalculatorParameter
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

        $parameter = new CalculatorParameter(
            $this->standardDate,
            new RateBasedCalculatorParameter(
                $this->point,
                $this->burden,
            ),
            new KogakuRyoyohiCalculatorParameter(
                nyugai:                   $this->nyugai,
                point:                    $this->point,
                generationClassification: $this->generationClassification,
                incomeClassification:     $this->incomeClassification,
                countState:               $this->kogakuCountState,
            ),
        );

        $this->clearState();

        $this->logger->debug('calculatorParameter generated', [
            'calculator parameter' => $parameter,
        ]);

        return $parameter;
    }

    private function clearState(): void
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
     * - 70歳到達月
     * - 75歳到達月
     */
    private function detectIsElderly(): void
    {
        $this->logger->debug('start detecting whether patient is elderly or not...');

        $this->patientAge ??= $this->patientBirthDate
            ? $this->standardDate->diff($this->patientBirthDate)->y
            : null;

        if (is_null($this->patientAge)) {
            $this->generationClassification =
                $this->incomeClassification?->isElderly()
                    ? GenerationClassification::LATE_ELDERLY()
                    : GenerationClassification::NORMAL();
            return;
        }

        $this->generationClassification = match (true) {
            $this->patientAge >= 75 => GenerationClassification::LATE_ELDERLY(),
            $this->patientAge >= 70 => GenerationClassification::EARLY_ELDERLY(),
            $this->patientAge <= 6  => GenerationClassification::PRESCHOOL(),
            default                 => GenerationClassification::NORMAL(),
        };
    }

    private function detectBurden(): void
    {
        $this->logger->debug("start detecting patient's burden rate...");

        if (!is_null($this->burden)) {
            $this->logger->debug('指定された患者割合', [
                'patient burden rate' => $this->burden,
            ]);
            return;
        }

        if ($this->generationClassification->isElderly() && $this->incomeClassification?->isComparableToNonEldery()) {
            $this->logger->debug('所得区分現役並みのため自動設定', [
                'patient burden rate' => $this->burden,
            ]);
            $this->burden = 0.3;
            return;
        }

        $this->burden = $this->generationClassification->getDefaultBurdenRate();

        $this->logger->debug('年齢区分から定率負担割合を計算', [
            'age of patient'      => $this->patientAge,
            'patient burden rate' => $this->burden,
        ]);
    }

    private function detectKogakuIncomeClassification(): void
    {
        $this->logger->debug('start detecting income classification...');

        $this->logger->debug('指定された所得区分', [
            'income classification' => $this->incomeClassificationKey,
        ]);

        if (!is_null($this->incomeClassificationKey)) {
            $this->logger->debug('指定された所得区分を適用', [
                'income classification' => $this->incomeClassification,
            ]);
            return;
        }

        $this->logger->debug('所得区分の指定なし', [
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

        $this->logger->debug('高齢受給者・後期高齢者のため自動適用', [
            'income classification' => $this->incomeClassification,
        ]);
    }

    private function convertIncomeClassificationFromKey(string $classificationKey): IncomeClassification
    {
        return $this->classificationMaster->findIncomeClassificationByKey($classificationKey);
    }
}
