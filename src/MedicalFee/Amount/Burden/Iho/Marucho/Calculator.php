<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\Marucho;

use IjiUtils\MedicalFee\Amount\Burden\Iho\CalculatorResult as UpperResult;

class Calculator
{
    public function calculate(
        CalculatorParameter $parameter,
        UpperResult         $upperResult // todo: replace with interface
    ): CalculatorResult {
        $limitAmount      = $parameter->getIncomeClassification()->getLimitAmount();
        $targetAmount     = $upperResult->getBurdenAmount();
        $isMaruchoApplied = $targetAmount->isGreaterThan($limitAmount);

        return new CalculatorResult(
            parameter:        $parameter,
            burdenAmount:     $isMaruchoApplied ? $limitAmount : $targetAmount,
            targetAmount:     $targetAmount,
            isMaruchoApplied: $isMaruchoApplied,
        );
    }
}
