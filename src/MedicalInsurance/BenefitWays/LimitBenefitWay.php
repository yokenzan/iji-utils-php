<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BenefitWays;

use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;

/**
 * 上限額による助成内容
 */
class LimitBenefitWay implements BenefitWayInterface
{
    use BenefitWayTrait;

    private Amount $limit;

    public function __construct(Amount $limit)
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritDoc}
     */
    public function getPatientBurdenDescription(): string
    {
        return '上限額';
    }

    /**
     * {@inheritDoc}
     */
    public function getInsurerBurdenDescription(): string
    {
        return '差額';
    }

    /**
     * {@inheritDoc}
     */
    public function getBurdenSummary(): string
    {
        return (string)$this->limit;
    }

    /**
     * {@inheritDoc}
     */
    private function calculateBurdenAmount(Input $_inputFromUpper): Amount
    {
        return $this->limit;
    }
}
