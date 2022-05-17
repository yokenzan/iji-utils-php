<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased;

use IjiUtils\MedicalFee\Amount\Amount;

class Calculator
{
    public function calculate(CalculatorParameter $parameter): CalculatorResult
    {
        return new CalculatorResult(
            $parameter,
            Amount::fromPoint($parameter->getPoint(), $parameter->getBurden())
        );
    }
}
