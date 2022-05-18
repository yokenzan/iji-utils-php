<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\Marucho;

use JsonSerializable;

/**
 * 医療保険マル長の窓口負担金計算をおこなうためのパラメタ
 */
class CalculatorParameter implements JsonSerializable
{
    private IncomeClassification $incomeClassification;

    public function __construct(IncomeClassification $incomeClassification)
    {
        $this->incomeClassification = $incomeClassification;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }

    public function getIncomeClassification(): IncomeClassification
    {
        return $this->incomeClassification;
    }
}
