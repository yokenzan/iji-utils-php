<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\Calculators;

use IjiUtils\MedicalInsurance\BenefitWays\BenefitCategory;
use IjiUtils\MedicalInsurance\BenefitWays\BenefitWayInterface;
use IjiUtils\MedicalInsurance\Insurance;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;

class Output
{
    private Input               $input;
    private Amount              $burdenAmount;
    private Insurance           $insurance;
    private bool                $isBenefited;
    private BenefitWayInterface $benefit;
    private BenefitCategory     $category;

    public function __construct(
        Input               $input,
        Amount              $burdenAmount,
        Insurance           $insurance,
        BenefitWayInterface $benefit,
        BenefitCategory     $category,
        bool                $isBenefited
    ) {
        $this->input        = $input;
        $this->burdenAmount = $burdenAmount;
        $this->insurance    = $insurance;
        $this->benefit      = $benefit;
        $this->category     = $category;
        $this->isBenefited  = $isBenefited;
    }

    public function getInput(): Input
    {
        return $this->input;
    }

    public function getBurdenAmount(): Amount
    {
        return $this->burdenAmount;
    }

    public function getBenefitedAmount(): Amount
    {
        return $this->isBenefited()
            ? $this->getTargetAmount()->sub($this->getBurdenAmount())
            : Amount::generate(0)
            ;
    }

    public function getTargetAmount(): Amount
    {
        return $this->getInput()->getTargetAmount();
    }

    public function getBenefit(): ?BenefitWayInterface
    {
        return $this->benefit;
    }

    public function isBenefited(): bool
    {
        return $this->isBenefited;
    }

    public function getCategory(): BenefitCategory
    {
        return $this->category;
    }
}
