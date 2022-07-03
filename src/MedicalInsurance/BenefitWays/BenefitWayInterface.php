<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BenefitWays;

use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\Calculators\Output;
use IjiUtils\MedicalInsurance\Insurance;

interface BenefitWayInterface
{
    public function calculate(Insurance $insurance, Input $inputFromUpper): Output;

    public function getPatientBurdenDescription(): string;

    public function getInsurerBurdenDescription(): string;

    public function getBurdenSummary(): string;
}
