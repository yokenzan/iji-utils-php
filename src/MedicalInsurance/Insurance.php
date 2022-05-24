<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance;

use Ds\Vector;
use IjiUtils\MedicalInsurance\BenefitWays\KogakuBenefitWay;
use IjiUtils\MedicalInsurance\BenefitWays\LimitBenefitWay;
use IjiUtils\MedicalInsurance\BenefitWays\RateBenefitWay;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * 助成
 */
class Insurance
{
    private string            $description;
    private ?RateBenefitWay   $rateBenefit;
    private ?KogakuBenefitWay $kogakuBenefit;
    private ?LimitBenefitWay  $limitBenefit;
    private InsurerType       $insurerType;

    public function __construct(
        InsurerType       $insurerType,
        string            $description,
        ?RateBenefitWay   $rateBenefit = null,
        ?KogakuBenefitWay $kogakuBenefit = null,
        ?LimitBenefitWay  $limitBenefit = null
    ) {
        $this->insurerType   = $insurerType;
        $this->description   = $description;
        $this->rateBenefit   = $rateBenefit;
        $this->kogakuBenefit = $kogakuBenefit;
        $this->limitBenefit  = $limitBenefit;

        if ($this->toBenefits()->isEmpty()) {
            throw new InvalidArgumentException('insurances must have one or more benefits.');
        }
        $this->insurerType = $insurerType;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function hasRateBenefit(): bool
    {
        return !is_null($this->rateBenefit);
    }

    public function hasKogakuBenefit(): bool
    {
        return !is_null($this->kogakuBenefit);
    }

    public function hasLimitBenefit(): bool
    {
        return !is_null($this->limitBenefit);
    }

    /**
     * @return Vector<InsuranceBenefit>
     */
    public function toBenefits(): Vector
    {
        $benefits = new Vector();

        if ($this->hasRateBenefit()) {
            $benefits->push(new InsuranceBenefit(
                $this->rateBenefit,
                $this,
                match (true) {
                    $this->insurerType->equals(InsurerType::IHO())  => InsurerType::IHO(),
                    $this->insurerType->equals(InsurerType::KOHI()) => InsurerType::KOHI(),
                    default => throw new UnexpectedValueException('invalid insurer type'),
                }
            ));
        }
        if ($this->hasKogakuBenefit()) {
            $benefits->push(new InsuranceBenefit(
                $this->kogakuBenefit,
                $this,
                InsurerType::KOGAKU()
            ));
        }
        if ($this->hasLimitBenefit()) {
            $benefits->push(new InsuranceBenefit(
                $this->limitBenefit,
                $this,
                match (true) {
                    $this->insurerType->equals(InsurerType::IHO())  => InsurerType::MARUCHO(),
                    $this->insurerType->equals(InsurerType::KOHI()) => InsurerType::KOHI(),
                    default => throw new UnexpectedValueException('invalid insurer type'),
                }
            ));
        }

        return $benefits;
    }
}
