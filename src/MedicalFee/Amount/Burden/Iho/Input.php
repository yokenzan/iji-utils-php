<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho;

use DateTimeInterface;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\Input as KogakuRyoyohiInput;
use IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased\Input as RateBasedInput;
use JsonSerializable;

/**
 * 医療保険の窓口負担金計算をおこなうためのパラメタ
 */
class Input implements JsonSerializable
{
    private DateTimeInterface  $standardDate;
    private RateBasedInput     $rateBasedInput;
    private KogakuRyoyohiInput $kogakuRyoyohiInput;

    public function __construct(
        DateTimeInterface  $standardDate,
        RateBasedInput     $rateBasedInput,
        KogakuRyoyohiInput $kogakuRyoyohiInput
    ) {
        $this->standardDate       = $standardDate;
        $this->rateBasedInput     = $rateBasedInput;
        $this->kogakuRyoyohiInput = $kogakuRyoyohiInput;
    }

    /**
     * 負担割合側のパラメタ
     */
    public function getRateBasedParameter(): RateBasedInput
    {
        return $this->rateBasedInput;
    }

    /**
     * 高額療養費側のパラメタ
     */
    public function getKogakuRyoyohiParameter(): KogakuRyoyohiInput
    {
        return $this->kogakuRyoyohiInput;
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
