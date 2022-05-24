<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BenefitWays;

use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\Calculators\Output;
use IjiUtils\MedicalInsurance\InsuranceBenefit;

interface BenefitWayInterface
{
    public function calculate(InsuranceBenefit $appliedBenefit, Input $inputFromUpper): Output;

    public function getPatientBurdenDescription(): string;

    public function getInsurerBurdenDescription(): string;
}
