<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BenefitWays;

use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\IncomeClassification;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\IncomeClassificationAttribute;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\IncomeClassificationAttributeMaster;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\Input as KogakuRyoyohiInput;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\KogakuCountState;
use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;

class KogakuBenefitWay implements BenefitWayInterface
{
    use BenefitWayTrait;

    private IncomeClassificationAttributeMaster $incomeClassifications;
    private IncomeClassification                $incomeClassification;
    private KogakuCountState                    $countState;

    /**
     * @param ?KogakuCountState $countState nullを渡した場合、多数回該当ではなく通常回と見なします。
     */
    public function __construct(
        IncomeClassification $incomeClassification,
        ?KogakuCountState    $countState = null
    ) {
        $this->incomeClassifications = new IncomeClassificationAttributeMaster();
        $this->incomeClassification  = $incomeClassification;
        $this->countState            = $countState ?: KogakuCountState::NORMAL();
    }

    /**
     * {@inheritDoc}
     */
    public function getPatientBurdenDescription(): string
    {
        $text = $this->incomeClassification->getName();

        if ($this->countState->isReduced()) {
            $text .= '・' . $this->countState->getName();
        }

        return $text;
    }

    /**
     * {@inheritDoc}
     */
    public function getInsurerBurdenDescription(): string
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getBurdenSummary(): string
    {
        return $this->getPatientBurdenDescription();
    }

    /**
     * {@inheritDoc}
     */
    private function calculateBurdenAmount(Input $inputFromUpper): Amount
    {
        $point          = $inputFromUpper->getPoint();
        $classification = $this->detectIncomeClassification($inputFromUpper);

        if (!$classification->hasTotalAmount()) {
            return $classification->getBasicAmount();
        }

        return Amount::fromPoint($point)
            ->sub($classification->getTotalAmount())
            ->divideBy(100)
            ->add($classification->getBasicAmount())
            ->round();
    }

    private function detectIncomeClassification(Input $inputFromUpper): IncomeClassificationAttribute
    {
        return $this->incomeClassifications->detect(new KogakuRyoyohiInput(
            nyugai:               $inputFromUpper->getNyugai(),
            point:                $inputFromUpper->getPoint(),
            incomeClassification: $this->incomeClassification,
            countState:           $this->countState
        ));
    }
}
