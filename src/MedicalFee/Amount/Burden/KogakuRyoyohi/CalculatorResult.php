<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi;

use IjiUtils\MedicalFee\Amount\Amount;
use JsonSerializable;

class CalculatorResult implements JsonSerializable
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

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
