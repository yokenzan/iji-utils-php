<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Point;

use Stringable;

class Point implements Stringable
{
    public static function generate(int|float $point): static
    {
        return new static((int)$point);
    }

    protected int $point;

    final public function __construct(int $point)
    {
        $this->point = $point;
    }

    public function toInt(): int
    {
        return (int)$this->point;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return number_format($this->toInt());
    }
}
