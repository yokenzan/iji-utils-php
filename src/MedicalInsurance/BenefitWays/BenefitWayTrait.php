<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BenefitWays;

use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\Calculators\Output;
use IjiUtils\MedicalInsurance\Insurance;
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
    public function calculate(Insurance $insurance, Input $inputFromUpper): Output
    {
        $burdenAmount = $this->calculateBurdenAmount($inputFromUpper);
        $targetAmount = $inputFromUpper->getTargetAmount();

        return new Output(
            input:        $inputFromUpper,
            burdenAmount: min($targetAmount, $burdenAmount),
            insurance:    $insurance,
            benefit:      $this,
            category:     $this->resolveCategory($insurance),
            isBenefited:  $targetAmount->isGreaterThan($burdenAmount)
        );
    }

    private function resolveCategory(Insurance $insurance): BenefitCategory
    {
        return BenefitCategory::fromBenefitWayAndInsurerType(
            benefitWay:  $this,
            insurerType: $insurance->getInsurerType()
        );
    }
}
