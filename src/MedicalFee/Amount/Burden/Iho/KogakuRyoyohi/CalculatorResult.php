<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi;

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

    /**
     * 一部負担金を返します
     */
    public function getBurdenAmount(): Amount
    {
        return $this->amount;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
