<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden;

use DateTimeInterface;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\CalculatorParameter as KogakuRyoyohiCalculatorParameter;
use IjiUtils\MedicalFee\Amount\Burden\RateBased\CalculatorParameter as RateBasedCalculatorParameter;

class CalculatorParameter
{
    private DateTimeInterface $standardDate;
    private PatientAttribute $patientAttribute;
    private RateBasedCalculatorParameter $rateBasedCalculatorParameter;
    private KogakuRyoyohiCalculatorParameter $kogakuRyoyohiCalculatorParameter;

    public function __construct(
        DateTimeInterface $standardDate,
        PatientAttribute $patientAttribute,
        RateBasedCalculatorParameter $rateBasedCalculatorParameter,
        KogakuRyoyohiCalculatorParameter $kogakuRyoyohiCalculatorParameter
    ) {
        $this->standardDate                 = $standardDate;
        $this->patientAttribute             = $patientAttribute;
        $this->rateBasedCalculatorParameter = $rateBasedCalculatorParameter;
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
}
