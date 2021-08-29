<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\RateBased;

use IjiUtils\MedicalFee\Point\Point;
use JsonSerializable;

class CalculatorParameter implements JsonSerializable
{
    private Point $point;

    private float $burden;

    public function __construct(Point $point, float $burden = 1.0)
    {
        $this->point  = $point;
        $this->burden = $burden;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getBurden(): float
    {
        return $this->burden;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
