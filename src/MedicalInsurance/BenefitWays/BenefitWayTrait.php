<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BenefitWays;

use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\Calculators\Output;
use IjiUtils\MedicalInsurance\InsuranceBenefit;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;

trait BenefitWayTrait
{
    /**
     * 患者負担額を計算する
     */
    abstract private function calculateBurdenAmount(Input $inputFromUpper): Amount;

    /**
     * {@inheritDoc}
     */
    public function calculate(InsuranceBenefit $appliedBenefit, Input $inputFromUpper): Output
    {
        $burdenAmount = $this->calculateBurdenAmount($inputFromUpper);
        $targetAmount = $inputFromUpper->getTargetAmount();

        return new Output(
            $inputFromUpper,
            min($targetAmount, $burdenAmount),
            $appliedBenefit,
            $targetAmount->isGreaterThan($burdenAmount)
        );
    }
}
