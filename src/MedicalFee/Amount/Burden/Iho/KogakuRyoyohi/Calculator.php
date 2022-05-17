<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi;

use IjiUtils\MedicalFee\Amount\Amount;

class Calculator
{
    private IncomeClassificationAttributeMaster $incomeClassifications;

    public function __construct(IncomeClassificationAttributeMaster $incomeClassifications)
    {
        $this->incomeClassifications = $incomeClassifications;
    }

    public function calculate(CalculatorParameter $parameter): CalculatorResult
    {
        $incomeClassificationAttribute = $this->incomeClassifications->detect($parameter);

        $amount = !$incomeClassificationAttribute->hasTotalAmount()
            ? $incomeClassificationAttribute->getBasicAmount()
            : Amount::fromPoint($parameter->getPoint())
                ->sub($incomeClassificationAttribute->getTotalAmount())
                ->divideBy(100)
                ->add($incomeClassificationAttribute->getBasicAmount())
                ->round();

        return new CalculatorResult($parameter, $amount);
    }
}
