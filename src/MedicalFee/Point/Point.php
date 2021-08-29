<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Point;

use JsonSerializable;
use Stringable;

class Point implements Stringable, JsonSerializable
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
    public function jsonSerialize()
    {
        return [
            'value'     => $this->toInt(),
            'formatted' => (string)$this,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return number_format($this->toInt());
    }
}
