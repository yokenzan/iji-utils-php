<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi;

use IjiUtils\MedicalFee\Amount\Amount;
use IjiUtils\MedicalFee\Point\Point;

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

    public function calculate(CalculatorParameter $parameter): CalculatorResult
    {
        $amount = $this->calculateAmount(
            $parameter->getPoint(),
            $this->detectIncomeClassification($parameter)
        );

        return new CalculatorResult($parameter, $amount);
    }

    private function detectIncomeClassification(CalculatorParameter $parameter): IncomeClassificationAttribute
    {
        return $this->incomeClassifications->detect($parameter);
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
