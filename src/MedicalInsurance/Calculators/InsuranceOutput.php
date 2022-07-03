<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\Calculators;

use ArrayIterator;
use Ds\Vector;
use IjiUtils\MedicalInsurance\BenefitWays\BenefitWayInterface;
use IjiUtils\MedicalInsurance\Insurance;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;
use IteratorAggregate;
use Traversable;

class InsuranceOutput implements IteratorAggregate
{
    /**
     * @var Vector<Output>
     */
    private Vector    $children;
    private Input     $input;
    private Insurance $insurance;

    public function __construct(Input $input, Insurance $insurance)
    {
        $this->input     = $input;
        $this->insurance = $insurance;
        $this->children  = new Vector();
    }

    public function getInput(): Input
    {
        return $this->input;
    }

    public function addChild(Output $output): void
    {
        $this->children->push($output);
    }

    public function getBurdenAmount(): Amount
    {
        return $this->last()->getBurdenAmount();
    }

    public function last(): ?Output
    {
        return $this->children->last();
    }

    public function getBenefitedAmount(): Amount
    {
        return $this->input->getTargetAmount()->sub($this->getBurdenAmount());
    }

    public function getTargetAmount(): Amount
    {
        return $this->getInput()->getTargetAmount();
    }

    public function isBenefited(): bool
    {
        foreach ($this->children as $child) {
            if ($child->isBenefited()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @return Traversable<Output>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->children->toArray());
    }

    public function getBenefit(): ?BenefitWayInterface
    {
        return null;
    }

    public function getInsurance(): Insurance
    {
        return $this->insurance;
    }
}
