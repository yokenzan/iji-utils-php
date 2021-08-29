<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi;

use MyCLabs\Enum\Enum;

/**
 * {@inheritDoc}
 *
 * @method static self UPPER_3()
 * @method static self UPPER_2()
 * @method static self UPPER_1()
 * @method static self MIDDLE()
 * @method static self LOWER_2()
 * @method static self LOWER_1()
 */
class ElderlyIncomeClassification extends Enum implements IncomeClassification
{
    private const UPPER_3 = 'upper-3';
    private const UPPER_2 = 'upper-2';
    private const UPPER_1 = 'upper-1';
    private const MIDDLE  = 'middle';
    private const LOWER_2 = 'lower-2';
    private const LOWER_1 = 'lower-1';

    private static $NAMES = [
        self::UPPER_3 => '現役並みⅢ',
        self::UPPER_2 => '現役並みⅡ',
        self::UPPER_1 => '現役並みⅠ',
        self::MIDDLE  => '一般',
        self::LOWER_2 => '低所得Ⅱ',
        self::LOWER_1 => '低所得Ⅰ',
    ];

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return self::$NAMES[$this->getValue()];
    }

    /**
     * {@inheritDoc}
     */
    public function isElderly(): bool
    {
        return true;
    }

    public function isComparableToNonEldery(): bool
    {
        return str_starts_with($this->getValue(), 'upper');
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'value' => $this->getValue(),
            'name'  => $this->getName(),
        ];
    }
}
