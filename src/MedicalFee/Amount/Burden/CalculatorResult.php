<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden;

use IjiUtils\MedicalFee\Amount\Amount;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\CalculatorResult as KogakuRyoyohiCalculatorResult;
use IjiUtils\MedicalFee\Amount\Burden\RateBased\CalculatorResult as RateBasedCalculatorResult;

class CalculatorResult
{
    private CalculatorParameter            $parameter;
    private RateBasedCalculatorResult      $rateBasedResult;
    private ?KogakuRyoyohiCalculatorResult $kogakuAppliedResult;
    private Amount                         $amount;
    private bool                           $isKogakuApplied;

    public function __construct(
        CalculatorParameter            $parameter,
        RateBasedCalculatorResult      $rateBasedResult,
        Amount                         $burdenAmount,
        ?KogakuRyoyohiCalculatorResult $kogakuAppliedResult = null,
        bool                           $isKogakuApplied = false
    ) {
        $this->parameter           = $parameter;
        $this->rateBasedResult     = $rateBasedResult;
        $this->kogakuAppliedResult = $kogakuAppliedResult;
        $this->amount              = $burdenAmount;
        $this->isKogakuApplied     = $isKogakuApplied;
    }

    public function getParameter(): CalculatorParameter
    {
        return $this->parameter;
    }

    public function getRateBasedResult(): RateBasedCalculatorResult
    {
        return $this->rateBasedResult;
    }

    public function getKogakuRyoyohiResult(): KogakuRyoyohiCalculatorResult
    {
        return $this->kogakuAppliedResult;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getReducedAmountByKogaku(): Amount
    {
        if (!$this->isKogakuApplied()) {
            return Amount::generate(0);
        }

        return $this->getRateBasedResult()->getAmount()->sub(
            $this->getKogakuRyoyohiResult()->getAmount()
        );
    }

    public function isKogakuApplied(): bool
    {
        return $this->isKogakuApplied;
    }
}
