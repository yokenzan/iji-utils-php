<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho;

use DateTimeInterface;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\CalculatorParameter as KogakuRyoyohiCalculatorParameter;
use IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased\CalculatorParameter as RateBasedCalculatorParameter;
use JsonSerializable;

/**
 * 医療保険の窓口負担金計算をおこなうためのパラメタ
 */
class CalculatorParameter implements JsonSerializable
{
    private DateTimeInterface                $standardDate;
    private RateBasedCalculatorParameter     $rateBasedCalculatorParameter;
    private KogakuRyoyohiCalculatorParameter $kogakuRyoyohiCalculatorParameter;

    public function __construct(
        DateTimeInterface                $standardDate,
        RateBasedCalculatorParameter     $rateBasedCalculatorParameter,
        KogakuRyoyohiCalculatorParameter $kogakuRyoyohiCalculatorParameter
    ) {
        $this->standardDate                     = $standardDate;
        $this->rateBasedCalculatorParameter     = $rateBasedCalculatorParameter;
        $this->kogakuRyoyohiCalculatorParameter = $kogakuRyoyohiCalculatorParameter;
    }

    /**
     * 負担割合側のパラメタ
     */
    public function getRateBasedParameter(): RateBasedCalculatorParameter
    {
        return $this->rateBasedCalculatorParameter;
    }

    /**
     * 高額療養費側のパラメタ
     */
    public function getKogakuRyoyohiParameter(): KogakuRyoyohiCalculatorParameter
    {
        return $this->kogakuRyoyohiCalculatorParameter;
    }

    /**
     * 基準日を返します
     */
    public function getStandardDate(): DateTimeInterface
    {
        return $this->standardDate;
    }

    /**
     * 高額療養費情報が設定されているか？
     */
    public function hasKogaku(): bool
    {
        return !is_null($this->getKogakuRyoyohiParameter()->getIncomeClassification());
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
