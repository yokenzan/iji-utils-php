<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\RateBased;

use IjiUtils\MedicalFee\Amount\Amount;

class CalculatorResult
{
    private CalculatorParameter $parameter;
    private Amount $amount;

    public function __construct(CalculatorParameter $parameter, Amount $amount)
    {
        $this->parameter = $parameter;
        $this->amount    = $amount;
    }

    public function getParameter(): CalculatorParameter
    {
        return $this->parameter;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }
}
