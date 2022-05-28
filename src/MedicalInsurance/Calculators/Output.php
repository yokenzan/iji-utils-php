<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\Calculators;

use IjiUtils\MedicalInsurance\InsuranceBenefit;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;

class Output
{
    private Input             $input;
    private Amount            $burdenAmount;
    private ?InsuranceBenefit $benefit;
    private bool              $isBenefited;

    public function __construct(
        Input             $input,
        Amount            $burdenAmount,
        ?InsuranceBenefit $benefit,
        bool              $isBenefited
    ) {
        $this->input        = $input;
        $this->burdenAmount = $burdenAmount;
        $this->benefit      = $benefit;
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

    public function getBenefit(): ?InsuranceBenefit
    {
        return $this->benefit;
    }

    public function isBenefited(): bool
    {
        return $this->isBenefited;
    }
}
