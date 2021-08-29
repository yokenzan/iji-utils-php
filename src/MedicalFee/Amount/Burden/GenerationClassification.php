<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden;

use MyCLabs\Enum\Enum;

/**
 * {@inheritDoc}
 *
 * @method static self NORMAL()
 * @method static self PRESCHOOL()
 * @method static self EARLY_ELDERLY()
 * @method static self LATE_ELDERLY()
 */
class GenerationClassification extends Enum
{
    private const NORMAL        = '一般';
    private const PRESCHOOL     = '未就学児';
    private const EARLY_ELDERLY = '高齢受給者';
    private const LATE_ELDERLY  = '後期高齢者';

    private static $DEFAULT_BURDEN_RATE = [
        self::NORMAL        => 0.3,
        self::PRESCHOOL     => 0.2,
        self::EARLY_ELDERLY => 0.2,
        self::LATE_ELDERLY  => 0.1,
    ];

    /**
     * そのうち外に出す
     */

    public function isElderly(): bool
    {
        return match ($this->getValue()) {
            self::EARLY_ELDERLY, self::LATE_ELDERLY => true,
            default                                 => false,
        };
    }

    public function isNonElderly(): bool
    {
        return !$this->isElderly();
    }

    public function getDefaultBurdenRate(): float
    {
        return self::$DEFAULT_BURDEN_RATE[$this->getValue()];
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'key'   => $this->getKey(),
            'value' => $this->getValue(),
        ];
    }
}
