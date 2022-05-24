<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\Calculators;

use IjiUtils\MedicalFee\Nyugai;
use IjiUtils\MedicalFee\ValueObjects\Point;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;

class Input
{
    private Point  $point;
    private Amount $targetAmount;
    private Nyugai $nyugai;

    /**
     * @param ?Amount $targetAmount nullを渡した場合、$pointの10割を$targetAmountにします。
     * @param ?Nyugai $nyugai       nullを渡した場合、外来と見なします。
     */
    public function __construct(
        Point   $point,
        ?Amount $targetAmount = null,
        ?Nyugai $nyugai       = null
    ) {
        $this->point        = $point;
        $this->targetAmount = $targetAmount ?: Amount::fromPoint($this->point);
        $this->nyugai       = $nyugai       ?: Nyugai::GAIRAI();
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getNyugai(): Nyugai
    {
        return $this->nyugai;
    }

    public function getTargetAmount(): Amount
    {
        return $this->targetAmount;
    }
}
