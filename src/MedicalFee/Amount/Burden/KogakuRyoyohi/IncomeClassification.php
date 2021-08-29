<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi;

interface IncomeClassification
{
    public function isElderly(): bool;

    public function isComparableToNonEldery(): bool;
}
