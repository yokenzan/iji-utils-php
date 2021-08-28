<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden;

use DateTimeImmutable;
use DateTimeInterface;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\CalculatorParameter as KogakuRyoyohiCalculatorParameter;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\IncomeClassificationAttributeMaster;
use IjiUtils\MedicalFee\Amount\Burden\RateBased\CalculatorParameter as RateBasedCalculatorParameter;
use IjiUtils\MedicalFee\Point\Point;
use Psr\Log\LoggerInterface;

class CalculatorParameterBuilder
{
    public const DEFAULT_PATIENT_BURDEN_RATE = 0.3;

    public ?Point $point;
    public ?DateTimeInterface $patientBirthDate;
    public ?DateTimeInterface $standardDate;
    public ?int $patientAge;
    public ?string $incomeClassification;
    public ?bool $isReduced;
    public ?bool $isElderly;
    public ?float $burden;
    public ?bool $isNyuin;
    private IncomeClassificationAttributeMaster $classificationMaster;
    private LoggerInterface $logger;

    public function __construct(
        IncomeClassificationAttributeMaster $classificationMaster,
        LoggerInterface $logger
    ) {
        $this->classificationMaster = $classificationMaster;
        $this->logger               = $logger;

        $this->clearState();
    }

    public function build(): CalculatorParameter
    {
        $this->logger->debug('start build CalculatorParameter...');

        $this->detectStandardDate();
        $this->detectIsElderly();
        $this->detectBurden();
        $this->detectKogakuIncomeClassification();

        $parameter = new CalculatorParameter(
            $this->standardDate,
            new PatientAttribute(
                $this->patientBirthDate,
                $this->patientAge,
                $this->isElderly,
            ),
            new RateBasedCalculatorParameter(
                $this->point,
                $this->burden,
            ),
            new KogakuRyoyohiCalculatorParameter(
                $this->point,
                $this->incomeClassification,
                $this->isNyuin ? 'nyuin' : 'gairai',
                $this->isReduced,
                $this->isElderly,
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
        $this->isNyuin = null;
        $this->point   = null;
        $this->burden  = null;

        $this->standardDate     = null;
        $this->patientBirthDate = null;
        $this->patientAge       = null;
        $this->isElderly        = null;

        $this->incomeClassification = null;
        $this->isReduced            = null;
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

        if (!is_null($this->isElderly)) {
            return;
        }

        $this->patientAge ??= $this->patientBirthDate
            ? $this->standardDate->diff($this->patientBirthDate)->y
            : null;

        if (!is_null($this->patientAge)) {
            $this->isElderly = $this->patientAge >= 70;
            return;
        }

        if (!is_null($this->incomeClassification)) {
            $this->isElderly = $this->classificationMaster
                ->detectIsElderlyOrNotByClassification($this->incomeClassification);
            return;
        }

        $this->isElderly = false;
    }

    private function detectBurden(): void
    {
        $this->logger->debug("start detecting patient's burden rate...");

        if (!is_null($this->burden)) {
            $this->logger->debug('患者割合が指定されています', [
                'patient burden rate' => $this->burden,
            ]);
            return;
        }

        if (str_starts_with($this->incomeClassification ?? '', 'upper')) {
            $this->logger->debug('所得区分現役並みのため自動設定', [
                'patient burden rate' => $this->burden,
            ]);
            $this->burden = 0.3;
            return;
        }

        if (!is_null($this->patientAge)) {
            $this->burden = match (true) {
                $this->patientAge >= 75 => 0.1,
                $this->patientAge >= 70 => 0.2,
                $this->patientAge <= 6  => 0.2,
                default                 => self::DEFAULT_PATIENT_BURDEN_RATE,
            };
            $this->logger->debug('年齢から定率負担割合を計算', [
                'age of patient'      => $this->patientAge,
                'patient burden rate' => $this->burden,
            ]);
            return;
        }

        $this->burden = self::DEFAULT_PATIENT_BURDEN_RATE;
        $this->logger->debug('デフォルトの負担割合を適用', [
            'age of patient'      => $this->patientAge,
            'patient burden rate' => $this->burden,
        ]);
    }

    private function detectKogakuIncomeClassification(): void
    {
        $this->logger->debug('start detecting income classification...');

        if (!is_null($this->incomeClassification)) {
            $this->logger->debug('指定された所得区分を適用', [
                'income classification' => $this->incomeClassification,
            ]);
            return;
        }

        $this->logger->debug('所得区分の指定なし', [
            'income classification' => $this->incomeClassification,
        ]);

        if (!$this->isElderly) {
            return;
        }

        $this->incomeClassification = match ($this->burden) {
            0.3      => 'upper-3',
            0.2, 0.1 => 'middle',
            default  => null,
        };

        $this->logger->debug('高齢受給者・後期高齢者のため自動適用', [
            'income classification' => $this->incomeClassification,
        ]);
    }
}
