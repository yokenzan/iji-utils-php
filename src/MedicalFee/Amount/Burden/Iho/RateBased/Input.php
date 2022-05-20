<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\RateBased;

use IjiUtils\MedicalFee\ValueObjects\Point;
use JsonSerializable;

/**
 * 医療保険の負担割合による助成内容を定義するパラメタ
 */
class Input implements JsonSerializable
{
    private Point $point;

    private float $burdenRate;

    public function __construct(Point $point, float $burdenRate = 1.0)
    {
        $this->point      = $point;
        $this->burdenRate = $burdenRate;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getBurdenRate(): float
    {
        return $this->burdenRate;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
