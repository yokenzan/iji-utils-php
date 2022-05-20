<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi;

use IjiUtils\MedicalFee\ValueObjects\Amount;

class IncomeClassificationAttribute
{
    private ?Amount $totalAmount;
    private Amount $basicAmount;

    public function __construct(?Amount $totalAmount, Amount $basicAmount)
    {
        $this->totalAmount = $totalAmount;
        $this->basicAmount = $basicAmount;
    }

    public function getTotalAmount(): ?Amount
    {
        return $this->totalAmount;
    }

    public function getBasicAmount(): Amount
    {
        return $this->basicAmount;
    }

    public function hasTotalAmount(): bool
    {
        return !is_null($this->totalAmount);
    }
}
