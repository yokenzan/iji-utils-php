<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Kohi;

use IjiUtils\MedicalFee\Amount\Amount;

class Calculator
{
    public function calclate(CalculatorParameter $parameter): CalculatorResult
    {
        // $upperResult  = $parameter->getUpperResult();
        // $upperBurden  = $upperResult->getAmount();
        $upperBurden  = $parameter->getUpperBurdenAmount();
        $amountLimit  = $parameter->getAmountLimit();
        $amountByRate = Amount::fromPoint($parameter->getPoint(), $parameter->getBurden());
        $burdenAmount = $amountByRate;

        if ($amountLimit && $burdenAmount->isGreaterThan($amountLimit)) {
            $burdenAmount = $amountLimit;
        }
        if ($upperBurden && $burdenAmount->isGreaterThan($upperBurden)) {
            $burdenAmount = $upperBurden;
        }

        return new CalculatorResult(
            parameter:     $parameter,
            amountByRate:  $amountByRate,
            amountByLimit: $amountLimit,
            burdenAmount:  $burdenAmount,
        );
    }
}
