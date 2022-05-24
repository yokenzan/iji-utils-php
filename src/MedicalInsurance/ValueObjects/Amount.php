<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\ValueObjects;

use IjiUtils\MedicalFee\ValueObjects\Point;
use JsonSerializable;
use Stringable;

/**
 * 負担・請求金額
 */
class Amount implements Stringable, JsonSerializable
{
    /**
     * 点→円の換算乗数
     */
    private const CONVERSION_RATE_FROM_POINT = 10;

    public static function generate(int|float $amount): static
    {
        return new static($amount);
    }

    public static function fromPoint(Point $point, BurdenRate|float $burden = 1.0): static
    {
        return static::generate(
            $point->toInt()
                * self::CONVERSION_RATE_FROM_POINT
                * ($burden instanceof BurdenRate ? $burden->toFloat() : $burden)
        );
    }

    protected float $amount;

    final public function __construct(int|float $amount)
    {
        $this->amount = (float)$amount;
    }

    public function round(int $precision = 0): static
    {
        return static::generate(round($this->amount, $precision));
    }

    public function toInt(): int
    {
        return (int)round($this->amount);
    }

    public function toFloat(): float
    {
        return (float)$this->amount;
    }

    public function add(self $other): static
    {
        return static::generate($this->amount + $other->amount);
    }

    public function sub(self $other): static
    {
        return static::generate($this->amount - $other->amount);
    }

    public function isGreaterThan(self $other, bool $orEquals = false): bool
    {
        $isGreater = $this->toFloat() > $other->toFloat();
        $equals    = $this->toFloat() === $other->toFloat();

        return $isGreater || ($orEquals ? $equals : false);
    }

    public function divideBy(int $diviser): static
    {
        return static::generate($this->amount / $diviser);
    }

    public function applyBurden(BurdenRate|float $burden): static
    {
        if ($burden instanceof BurdenRate) {
            return static::generate($this->amount * $burden->toFloat());
        }
        return static::generate($this->amount * $burden);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return [
            'int_value'   => $this->toInt(),
            'float_value' => $this->toFloat(),
            'formatted'   => (string)$this,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return '\\' . number_format($this->toInt());
    }
}
