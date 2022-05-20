<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\Marucho;

use IjiUtils\MedicalFee\ValueObjects\Amount;
use IjiUtils\MedicalFee\Amount\Burden\Contracts\BurdenBreakdownInterface;
use IjiUtils\MedicalFee\ValueObjects\Point;
use JsonSerializable;

class CalculatorResult implements JsonSerializable, BurdenBreakdownInterface
{
    /**
     * @var CalculatorParameter
     */
    private $parameter;
    /**
     * @var Amount
     */
    private $burdenAmount;
    /**
     * @var Amount
     */
    private $targetAmount;
    /**
     * @var bool
     */
    private $isMaruchoApplied;

    public function __construct(
        CalculatorParameter $parameter,
        Amount              $burdenAmount,
        Amount              $targetAmount,
        bool                $isMaruchoApplied = false
    ) {
        $this->parameter        = $parameter;
        $this->burdenAmount     = $burdenAmount;
        $this->targetAmount     = $burdenAmount;
        $this->isMaruchoApplied = $isMaruchoApplied;
    }

    public function getParameter(): CalculatorParameter
    {
        return $this->parameter;
    }

    /**
     * {@inheritDoc}
     */
    public function getBurdenAmount(): Amount
    {
        return $this->burdenAmount;
    }

    /**
     * {@inheritDoc}
     */
    public function getDiffBetweenRateAndLimit(): ?Amount
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function providesByLimit(): bool
    {
        return $this->isMaruchoApplied;
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
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function hasSubsidyByLimit(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function providesByRate(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getBurdenRate(): float
    {
        return 0.0;
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
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getBurdenAmountByLimit(): ?Amount
    {
        return $this->isMaruchoApplied
            ? $this->burdenAmount
            : null;
    }

    public function getPoint(): Point
    {
        return Point::generate(0);
        // return $this->getParameter()->getPoint();
    }

    public function getTargetAmount(): Amount
    {
        return $this->targetAmount;
    }
}
