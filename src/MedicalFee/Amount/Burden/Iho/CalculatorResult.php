<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho;

use IjiUtils\MedicalFee\ValueObjects\Amount;
use IjiUtils\MedicalFee\Amount\Burden\Contracts\BurdenBreakdownInterface;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\CalculatorResult as KogakuRyoyohiCalculatorResult;
use IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased\CalculatorResult as RateBasedCalculatorResult;
use IjiUtils\MedicalFee\ValueObjects\Point;
use JsonSerializable;

class CalculatorResult implements JsonSerializable, BurdenBreakdownInterface
{
    private Input                          $input;
    private RateBasedCalculatorResult      $rateBasedResult;
    private ?KogakuRyoyohiCalculatorResult $kogakuAppliedResult;
    private Amount                         $amount;
    private bool                           $isKogakuApplied;

    public function __construct(
        Input                          $input,
        RateBasedCalculatorResult      $rateBasedResult,
        Amount                         $burdenAmount,
        ?KogakuRyoyohiCalculatorResult $kogakuAppliedResult = null,
        bool                           $isKogakuApplied = false
    ) {
        $this->input               = $input;
        $this->rateBasedResult     = $rateBasedResult;
        $this->kogakuAppliedResult = $kogakuAppliedResult;
        $this->amount              = $burdenAmount;
        $this->isKogakuApplied     = $isKogakuApplied;
    }

    public function getParameter(): Input
    {
        return $this->input;
    }

    public function getRateBasedResult(): RateBasedCalculatorResult
    {
        return $this->rateBasedResult;
    }

    public function getKogakuRyoyohiResult(): KogakuRyoyohiCalculatorResult
    {
        return $this->kogakuAppliedResult;
    }

    /**
     * {@inheritDoc}
     */
    public function getBurdenAmount(): Amount
    {
        return $this->amount;
    }

    /**
     * {@inheritDoc}
     */
    public function getDiffBetweenRateAndLimit(): ?Amount
    {
        if (!$this->providesByLimit()) {
            return null;
        }

        return $this->getRateBasedResult()->getBurdenAmount()->sub(
            $this->getKogakuRyoyohiResult()->getBurdenAmount()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function providesByLimit(): bool
    {
        return $this->isKogakuApplied;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }

    /**
     * {@inheritDoc}
     */
    public function hasSubsidyByRate(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function hasSubsidyByLimit(): bool
    {
        return $this->getParameter()->hasKogaku();
    }

    /**
     * {@inheritDoc}
     */
    public function providesByRate(): bool
    {
        return (int)$this->getBurdenRate() !== 1;
    }

    /**
     * {@inheritDoc}
     */
    public function getBurdenRate(): float
    {
        return $this->getParameter()->getRateBasedParameter()->getBurdenRate();
    }

    /**
     * {@inheritDoc}
     */
    // public function getLimitAmount(): ?Amount
    // {
    // }

    /**
     * {@inheritDoc}
     */
    public function getBurdenAmountByRate(): ?Amount
    {
        return $this->getRateBasedResult()->getBurdenAmount();
    }

    /**
     * {@inheritDoc}
     */
    public function getBurdenAmountByLimit(): ?Amount
    {
        return $this->hasSubsidyByLimit()
            ? $this->getKogakuRyoyohiResult()->getBurdenAmount()
            : null;
    }

    public function getPoint(): Point
    {
        return $this->getParameter()->getRateBasedParameter()->getPoint();
    }

    public function getTargetAmount(): Amount
    {
        return Amount::fromPoint($this->getParameter()->getRateBasedParameter()->getPoint());
    }
}
