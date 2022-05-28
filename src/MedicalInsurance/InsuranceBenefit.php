<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance;

use IjiUtils\MedicalInsurance\BenefitWays\BenefitCategory;
use IjiUtils\MedicalInsurance\BenefitWays\BenefitWayInterface;
use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\Calculators\Output;

class InsuranceBenefit
{
    private BenefitWayInterface $benefit;
    private Insurance           $insurance;
    private BenefitCategory     $category;

    public function __construct(BenefitWayInterface $benefit, Insurance $insurance, BenefitCategory $category)
    {
        $this->benefit   = $benefit;
        $this->insurance = $insurance;
        $this->category  = $category;
    }

    public function calculate(Input $inputFromUpper): Output
    {
        return $this->benefit->calculate($this, $inputFromUpper);
    }

    public function getPatientBurdenDescription(): string
    {
        return $this->benefit->getPatientBurdenDescription();
    }

    public function getInsurerBurdenDescription(): string
    {
        return $this->benefit->getInsurerBurdenDescription();
    }

    public function getBurdenSummary(): string
    {
        return $this->benefit->getBurdenSummary();
    }

    public function getCategory(): BenefitCategory
    {
        return $this->category;
    }

    public function getInsuranceDescription(): string
    {
        return $this->insurance->getDescription();
    }
}
