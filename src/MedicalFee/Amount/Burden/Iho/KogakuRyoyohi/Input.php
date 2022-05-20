<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi;

use IjiUtils\MedicalFee\Amount\Burden\GenerationClassification;
use IjiUtils\MedicalFee\Nyugai;
use IjiUtils\MedicalFee\ValueObjects\Point;
use JsonSerializable;

/**
 * 医療保険の高額療養費所得区分による助成内容を定義するパラメタ
 */
class Input implements JsonSerializable
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

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
