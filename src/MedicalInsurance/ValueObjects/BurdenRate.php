<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * 負担割合
 */
class BurdenRate implements Stringable, JsonSerializable
{
    public static function generate(int|float $rate): self
    {
        return new self((float)$rate);
    }

    protected float $rate;

    public function __construct(float $rate = 0.0)
    {
        if ($rate > 1.0 || $rate < 0.0) {
            throw new InvalidArgumentException('burden rate must be between 0.0 ~ 1.0.');
        }

        $this->rate = $rate;
    }

    public function toFloat(): float
    {
        return (float)$this->rate;
    }

    public function sub(self $other): self
    {
        return self::generate($this->rate - $other->rate);
    }

    public function opposite(): self
    {
        return self::generate(1.0 - $this->rate);
    }

    public function toPercentage(): string
    {
        return sprintf('%d%%', $this->rate * 100);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return [
            'float_value' => $this->toFloat(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return sprintf('%.1f', $this->rate);
    }
}
