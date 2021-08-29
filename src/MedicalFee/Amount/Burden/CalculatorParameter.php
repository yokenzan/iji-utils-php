<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden;

use DateTimeInterface;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\CalculatorParameter as KogakuRyoyohiCalculatorParameter;
use IjiUtils\MedicalFee\Amount\Burden\RateBased\CalculatorParameter as RateBasedCalculatorParameter;
use JsonSerializable;

class CalculatorParameter implements JsonSerializable
{
    private DateTimeInterface                $standardDate;
    private RateBasedCalculatorParameter     $rateBasedCalculatorParameter;
    private KogakuRyoyohiCalculatorParameter $kogakuRyoyohiCalculatorParameter;

    public function __construct(
        DateTimeInterface                $standardDate,
        RateBasedCalculatorParameter     $rateBasedCalculatorParameter,
        KogakuRyoyohiCalculatorParameter $kogakuRyoyohiCalculatorParameter
    ) {
        $this->standardDate                     = $standardDate;
        $this->rateBasedCalculatorParameter     = $rateBasedCalculatorParameter;
        $this->kogakuRyoyohiCalculatorParameter = $kogakuRyoyohiCalculatorParameter;
    }

    public function getRateBasedParameter(): RateBasedCalculatorParameter
    {
        return $this->rateBasedCalculatorParameter;
    }

    public function getKogakuRyoyohiParameter(): KogakuRyoyohiCalculatorParameter
    {
        return $this->kogakuRyoyohiCalculatorParameter;
    }

    public function getStandardDate(): DateTimeInterface
    {
        return $this->standardDate;
    }

    public function hasKogaku(): bool
    {
        return !is_null($this->getKogakuRyoyohiParameter()->getIncomeClassification());
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
