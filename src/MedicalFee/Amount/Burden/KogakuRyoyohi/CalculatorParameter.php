<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi;

use IjiUtils\MedicalFee\Point\Point;

class CalculatorParameter
{
    private string $nyugai;
    private ?string $incomeClassification;
    private bool $isReduced;
    private ?bool $isElderly;
    private Point $point;

    public function __construct(
        Point $point,
        ?string $incomeClassification,
        string $nyugai = 'gairai',
        bool $isReduced = false,
        ?bool $isElderly = null,
    ) {
        $this->point                = $point;
        $this->incomeClassification = $incomeClassification;
        $this->nyugai               = $nyugai;
        $this->isReduced            = $isReduced;
        $this->isElderly            = $isElderly;
    }

    public function getNyugai(): string
    {
        return str_starts_with($this->nyugai, 'n') ? 'nyuin' : 'gairai';
    }

    public function isReduced(): bool
    {
        return $this->isReduced;
    }

    public function isElderly(): ?bool
    {
        return $this->isElderly;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getIncomeClassification(): ?string
    {
        return $this->incomeClassification;
    }
}
