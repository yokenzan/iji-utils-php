<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi;

use IjiUtils\MedicalInsurance\ValueObjects\Amount;
use JsonSerializable;

class CalculatorResult implements JsonSerializable
{
    private Input  $input;
    private Amount $amount;

    public function __construct(Input $input, Amount $amount)
    {
        $this->input  = $input;
        $this->amount = $amount;
    }

    public function getParameter(): Input
    {
        return $this->input;
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
