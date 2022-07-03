<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BenefitWays;

use IjiUtils\MedicalInsurance\InsurerType;
use MyCLabs\Enum\Enum;
use UnexpectedValueException;

/**
 * {@inheritDoc}
 *
 * @method static self RATE()
 * @method static self KOGAKU()
 * @method static self MARUCHO()
 * @method static self LIMIT()
 */
class BenefitCategory extends Enum
{
    private const RATE    = '負担割合';
    private const KOGAKU  = '高額療養費';
    private const MARUCHO = 'マル長';
    private const LIMIT   = '上限額';

    public static function fromBenefitWayAndInsurerType(BenefitWayInterface $benefitWay, InsurerType $insurerType): self
    {
        if ($insurerType->equals(InsurerType::IHO()) && $benefitWay instanceof LimitBenefitWay) {
            return self::MARUCHO();
        }

        if ($benefitWay instanceof RateBenefitWay) {
            return self::RATE();
        }

        if ($benefitWay instanceof KogakuBenefitWay) {
            return self::KOGAKU();
        }

        if ($benefitWay instanceof LimitBenefitWay) {
            return self::LIMIT();
        }

        throw new UnexpectedValueException('invalid');
    }

    public function isKogaku(): bool
    {
        return $this->equals(BenefitCategory::KOGAKU()) || $this->equals(BenefitCategory::MARUCHO());
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
