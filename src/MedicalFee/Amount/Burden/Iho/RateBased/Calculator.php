<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased;

use IjiUtils\MedicalFee\ValueObjects\Amount;

class Calculator
{
    public function calculate(Input $input): CalculatorResult
    {
        return new CalculatorResult($input, $this->calculateBurdenAmount($input));
    }

    private function calculateBurdenAmount(Input $input): Amount
    {
        return Amount::fromPoint($input->getPoint(), $input->getBurdenRate());
    }
}
