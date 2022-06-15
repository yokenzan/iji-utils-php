<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance;

use MyCLabs\Enum\Enum;

/**
 * {@inheritDoc}
 *
 * @method static self IHO()
 * @method static self KOHI()
 */
class InsurerType extends Enum
{
    private const IHO  = '医保';
    private const KOHI = '公費';

    /**
     * @var array<string, string>
     */
    private static array $NAMES = [
        self::IHO  => '医療保険',
        self::KOHI => '公費',
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
    public function jsonSerialize(): mixed
    {
        return [
            'key'   => $this->getKey(),
            'value' => $this->getValue(),
        ];
    }
}
