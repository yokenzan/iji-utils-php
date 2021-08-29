<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi;

use IjiUtils\MedicalFee\Amount\Burden\GenerationClassification;
use IjiUtils\MedicalFee\Nyugai;
use IjiUtils\MedicalFee\Point\Point;

class CalculatorParameter
{
    private Nyugai                   $nyugai;
    private GenerationClassification $generationClassification;
    private Point                    $point;
    private ?IncomeClassification    $incomeClassification;
    private ?KogakuCountState        $countState;

    public function __construct(
        Nyugai                   $nyugai,
        Point                    $point,
        GenerationClassification $generationClassification,
        ?IncomeClassification    $incomeClassification = null,
        ?KogakuCountState        $countState = null,
    ) {
        $this->nyugai                   = $nyugai;
        $this->point                    = $point;
        $this->generationClassification = $generationClassification;
        $this->incomeClassification     = $incomeClassification;
        $this->countState               = $countState;
    }

    public function getNyugai(): Nyugai
    {
        return $this->nyugai;
    }

    public function isElderly(): bool
    {
        return $this->generationClassification->isElderly();
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function hasKogaku(): bool
    {
        return !is_null($this->getIncomeClassification())
            && !is_null($this->getCountState())
            ;
    }

    /**
     * @return null|IncomeClassification|NonElderlyIncomeClassification|ElderlyIncomeClassification
     */
    public function getIncomeClassification(): ?IncomeClassification
    {
        return $this->incomeClassification;
    }

    public function getCountState(): ?KogakuCountState
    {
        return $this->countState;
    }
}
