<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BenefitWays;

use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;
use IjiUtils\MedicalInsurance\ValueObjects\BurdenRate;

/**
 * 負担割合による助成内容
 */
class RateBenefitWay implements BenefitWayInterface
{
    use BenefitWayTrait;

    private BurdenRate $burdenRate;

    public function __construct(BurdenRate $burdenRate)
    {
        $this->burdenRate = $burdenRate;
    }

    /**
     * {@inheritDoc}
     */
    public function getPatientBurdenDescription(): string
    {
        return sprintf('x%s', $this->burdenRate);
    }

    /**
     * {@inheritDoc}
     */
    public function getInsurerBurdenDescription(): string
    {
        return sprintf('x%s', $this->burdenRate->opposite());
    }

    /**
     * {@inheritDoc}
     */
    private function calculateBurdenAmount(Input $inputFromUpper): Amount
    {
        return $this->roundBurdenAmount(Amount::fromPoint($inputFromUpper->getPoint(), $this->burdenRate));
    }

    /**
     * 負担割合で算出された窓口負担金は一の位を四捨五入する
     */
    private function roundBurdenAmount(Amount $burdenAmount): Amount
    {
        return $burdenAmount->round(-1);
    }

    // public function getBurdenRate(): BurdenRate
    // {
    //     return $this->burdenRate;
    // }
}
