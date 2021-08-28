<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden;

use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\Calculator as KogakuRyoyohiCalculator;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\CalculatorParameter as KogakuRyoyohiCalculatorParameter;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\CalculatorResult as KogakuRyoyohiCalculatorResult;
use IjiUtils\MedicalFee\Amount\Burden\RateBased\Calculator as RateBasedCalculator;
use IjiUtils\MedicalFee\Amount\Burden\RateBased\CalculatorParameter as RateBasedCalculatorParameter;
use IjiUtils\MedicalFee\Amount\Burden\RateBased\CalculatorResult as RateBasedCalculatorResult;

class Calculator
{
    private RateBasedCalculator $rateBasedCalculator;
    private KogakuRyoyohiCalculator $kogakuAppliedCalculator;

    public function __construct(
        RateBasedCalculator     $rateBasedCalculator,
        KogakuRyoyohiCalculator $kogakuAppliedCalculator
    ) {
        $this->rateBasedCalculator     = $rateBasedCalculator;
        $this->kogakuAppliedCalculator = $kogakuAppliedCalculator;
    }

    public function calculate(CalculatorParameter $parameter): CalculatorResult
    {
        $rateBasedResult = $this->calculateRateBased(
            $parameter->getRateBasedParameter()
        );

        if (!$parameter->hasKogaku()) {
            return new CalculatorResult(
                parameter:       $parameter,
                rateBasedResult: $rateBasedResult,
                burdenAmount:    $rateBasedResult->getAmount()
            );
        }

        $kogakuAppliedResult = $this->calculateKogakuRyoyohi(
            $parameter->getKogakuRyoyohiParameter()
        );
        $isKogakuApplied     = $rateBasedResult->getAmount()->isGreaterThan(
            $kogakuAppliedResult->getAmount(),
        );
        $burdenAmount        = $isKogakuApplied
            ? $kogakuAppliedResult->getAmount()
            : $rateBasedResult->getAmount();

        return new CalculatorResult(
            parameter:           $parameter,
            rateBasedResult:     $rateBasedResult,
            burdenAmount:        $burdenAmount,
            kogakuAppliedResult: $kogakuAppliedResult,
            isKogakuApplied:     $isKogakuApplied
        );
    }

    private function calculateRateBased(
        RateBasedCalculatorParameter $parameter
    ): RateBasedCalculatorResult {
        return $this->rateBasedCalculator->calculate($parameter);
    }

    private function calculateKogakuRyoyohi(
        KogakuRyoyohiCalculatorParameter $parameter
    ): KogakuRyoyohiCalculatorResult {
        return $this->kogakuAppliedCalculator->calculate($parameter);
    }
}
