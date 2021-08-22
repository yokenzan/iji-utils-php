<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Point;

use Stringable;

class Point implements Stringable
{
    protected int $point;

    public static function generate(int|float $point): static
    {
        return new static((int)$point);
    }

    final public function __construct(int $point)
    {
        $this->point = $point;
    }

    public function __toString()
    {
        return number_format($this->toInt());
    }

    public function toInt(): int
    {
        return (int)$this->point;
    }
}
