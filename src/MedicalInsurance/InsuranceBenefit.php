<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance;

use IjiUtils\MedicalInsurance\BenefitWays\BenefitWayInterface;
use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\Calculators\Output;

class InsuranceBenefit
{
    private BenefitWayInterface $benefit;
    private Insurance           $insurance;
    private InsurerType         $insurerType;

    public function __construct(BenefitWayInterface $benefit, Insurance $insurance, InsurerType $insurerType)
    {
        $this->benefit     = $benefit;
        $this->insurance   = $insurance;
        $this->insurerType = $insurerType;
    }

    public function calculate(Input $inputFromUpper): Output
    {
        return $this->benefit->calculate($this, $inputFromUpper);
    }

    public function getInsurerType(): InsurerType
    {
        return $this->insurerType;
    }

    public function getPatientBurdenDescription(): string
    {
        return $this->benefit->getPatientBurdenDescription();
    }

    public function getInsurerBurdenDescription(): string
    {
        return $this->benefit->getInsurerBurdenDescription();
    }
}
