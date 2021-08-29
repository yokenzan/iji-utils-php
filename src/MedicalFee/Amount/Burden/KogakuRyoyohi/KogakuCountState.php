<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi;

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

    private const NAMES = [
        self::NORMAL  => '通常回',
        self::REDUCED => '多数回',
    ];

    public function getName(): string
    {
        return self::NAMES[$this->getValue()];
    }

    public function isReduced(): bool
    {
        return $this->getValue() === self::REDUCED;
    }
}
