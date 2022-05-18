<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Kohi;

use IjiUtils\MedicalFee\Amount\Amount;
use IjiUtils\MedicalFee\Amount\Burden\CalculatorResult;
use IjiUtils\MedicalFee\Point\Point;
use JsonSerializable;

class CalculatorParameter implements JsonSerializable
{
    private Point             $point;
    private ?Amount           $amountLimit;
    private ?Amount           $upperBurdenAmount;
    private float             $burden;

    public function __construct(
        Point   $point,
        ?Amount $amountLimit,
        ?Amount $upperBurdenAmount,
        float   $burden = 1.0
    ) {
        $this->point             = $point;
        $this->amountLimit       = $amountLimit;
        $this->upperBurdenAmount = $upperBurdenAmount;
        // $this->upperResult = $upperResult;
        $this->burden            = $burden;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getBurden(): float
    {
        return $this->burden;
    }

    public function getAmountLimit(): ?Amount
    {
        return $this->amountLimit;
    }

    public function getUpperBurdenAmount(): ?Amount
    {
        return $this->upperBurdenAmount;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    // public function getUpperResult(): ?CalculatorResult
    // {
    //     return $this->upperResult;
    // }
}
