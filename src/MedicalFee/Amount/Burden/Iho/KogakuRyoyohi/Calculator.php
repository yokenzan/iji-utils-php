<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi;

use IjiUtils\MedicalFee\ValueObjects\Amount;
use IjiUtils\MedicalFee\ValueObjects\Point;

/**
 * 高額療養費限度額を計算する
 */
class Calculator
{
    private IncomeClassificationAttributeMaster $incomeClassifications;

    public function __construct(IncomeClassificationAttributeMaster $incomeClassifications)
    {
        $this->incomeClassifications = $incomeClassifications;
    }

    public function calculate(Input $input): CalculatorResult
    {
        $amount = $this->calculateAmount(
            $input->getPoint(),
            $this->detectIncomeClassification($input)
        );

        return new CalculatorResult($input, $amount);
    }

    private function detectIncomeClassification(Input $input): IncomeClassificationAttribute
    {
        return $this->incomeClassifications->detect($input);
    }

    private function calculateAmount(
        Point                         $point,
        IncomeClassificationAttribute $incomeClassificationAttribute
    ): Amount {
        if (!$incomeClassificationAttribute->hasTotalAmount()) {
            return $incomeClassificationAttribute->getBasicAmount();
        }

        return Amount::fromPoint($point)
            ->sub($incomeClassificationAttribute->getTotalAmount())
            ->divideBy(100)
            ->add($incomeClassificationAttribute->getBasicAmount())
            ->round();
    }
}
