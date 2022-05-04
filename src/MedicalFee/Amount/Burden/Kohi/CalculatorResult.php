<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Kohi;

use IjiUtils\MedicalFee\Amount\Amount;
use JsonSerializable;

class CalculatorResult implements JsonSerializable
{
    private CalculatorParameter $parameter;
    private Amount              $amountByRate;
    private ?Amount             $amountByLimit;
    private Amount              $burdenAmount;

    public function __construct(
        CalculatorParameter $parameter,
        Amount              $amountByRate,
        ?Amount             $amountByLimit,
        Amount              $burdenAmount
    ) {
        $this->parameter     = $parameter;
        $this->amountByRate  = $amountByRate;
        $this->amountByLimit = $amountByLimit;
        $this->burdenAmount  = $burdenAmount;
    }

    public function getParameter(): CalculatorParameter
    {
        return $this->parameter;
    }

    public function getAmountByRate(): Amount
    {
        return $this->amountByRate;
    }

    public function getAmountByLimit(): ?Amount
    {
        return $this->amountByLimit;
    }

    public function getBurdenAmount(): Amount
    {
        return $this->burdenAmount;
    }

    public function getUpperBurdenAmount(): ?Amount
    {
        return $this->parameter->getUpperBurdenAmount();
    }

    public function isKohiApplied(): bool
    {
        $upperBurdenAmount = $this->getUpperBurdenAmount();

        if (is_null($upperBurdenAmount)) {
            return true;
        }

        return $upperBurdenAmount->isGreaterThan($this->burdenAmount);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
