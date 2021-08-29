<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee;

use MyCLabs\Enum\Enum;

/**
 * {@inheritDoc}
 *
 * @method static self GAIRAI()
 * @method static self NYUIN()
 */
class Nyugai extends Enum
{
    private const GAIRAI = '外来';
    private const NYUIN  = '入院';

    public function isNyuin(): bool
    {
        return $this->getValue() === self::NYUIN;
    }
}
