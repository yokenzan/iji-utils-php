<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi;

use MyCLabs\Enum\Enum;

/**
 * {@inheritDoc}
 *
 * @method static self NORMAL()
 * @method static self REDUCED()
 */
class KogakuCountState extends Enum
{
    private const NORMAL  = 'normal';
    private const REDUCED = 'reudced';

    /**
     * @var array<string, string>
     */
    private static array $NAMES = [
        self::NORMAL  => '通常回',
        self::REDUCED => '多数回',
    ];

    public function getName(): string
    {
        return self::$NAMES[$this->getValue()];
    }

    public function isReduced(): bool
    {
        return $this->getValue() === self::REDUCED;
    }
}
