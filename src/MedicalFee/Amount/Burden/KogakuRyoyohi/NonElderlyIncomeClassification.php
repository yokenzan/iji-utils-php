<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi;

use MyCLabs\Enum\Enum;

/**
 * {@inheritDoc}
 *
 * @method static self A()
 * @method static self I()
 * @method static self U()
 * @method static self E()
 * @method static self O()
 */
class NonElderlyIncomeClassification extends Enum implements IncomeClassification
{
    private const A = 'a';
    private const I = 'i';
    private const U = 'u';
    private const E = 'e';
    private const O = 'o';

    /**
     * @var array<string, string>
     */
    private static array $NAMES = [
        self::A => '区分ア',
        self::I => '区分イ',
        self::U => '区分ウ',
        self::E => '区分エ',
        self::O => '区分オ',
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
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isComparableToNonEldery(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return [
            'value' => $this->getValue(),
            'name'  => $this->getName(),
        ];
    }
}
