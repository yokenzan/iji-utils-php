<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho;

use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\Calculator as KogakuRyoyohiCalculator;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\CalculatorResult as KogakuRyoyohiCalculatorResult;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\Input as KogakuRyoyohiInput;
use IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased\Calculator as RateBasedCalculator;
use IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased\CalculatorResult as RateBasedCalculatorResult;
use IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased\Input as RateBasedInput;

class Calculator
{
    private RateBasedCalculator     $rateBasedCalculator;
    private KogakuRyoyohiCalculator $kogakuAppliedCalculator;

    public function __construct(
        RateBasedCalculator     $rateBasedCalculator,
        KogakuRyoyohiCalculator $kogakuAppliedCalculator
    ) {
        $this->rateBasedCalculator     = $rateBasedCalculator;
        $this->kogakuAppliedCalculator = $kogakuAppliedCalculator;
    }

    public function calculate(Input $input): CalculatorResult
    {
        $rateBasedResult = $this->calculateRateBased(
            $input->getRateBasedParameter()
        );

        if (!$input->hasKogaku()) {
            return new CalculatorResult(
                input:           $input,
                rateBasedResult: $rateBasedResult,
                burdenAmount:    $rateBasedResult->getBurdenAmount()
            );
        }

        $kogakuAppliedResult = $this->calculateKogakuRyoyohi(
            $input->getKogakuRyoyohiParameter()
        );
        $isKogakuApplied     = $rateBasedResult->getBurdenAmount()->isGreaterThan(
            $kogakuAppliedResult->getBurdenAmount(),
        );
        $burdenAmount        = $isKogakuApplied
            ? $kogakuAppliedResult->getBurdenAmount()
            : $rateBasedResult->getBurdenAmount();

        return new CalculatorResult(
            input:               $input,
            rateBasedResult:     $rateBasedResult,
            burdenAmount:        $burdenAmount,
            kogakuAppliedResult: $kogakuAppliedResult,
            isKogakuApplied:     $isKogakuApplied
        );
    }

    private function calculateRateBased(
        RateBasedInput $input
    ): RateBasedCalculatorResult {
        return $this->rateBasedCalculator->calculate($input);
    }

    private function calculateKogakuRyoyohi(
        KogakuRyoyohiInput $input
    ): KogakuRyoyohiCalculatorResult {
        return $this->kogakuAppliedCalculator->calculate($input);
    }
}
