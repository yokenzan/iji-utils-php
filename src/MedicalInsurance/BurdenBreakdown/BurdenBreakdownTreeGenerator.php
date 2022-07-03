<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BurdenBreakdown;

use IjiUtils\MedicalInsurance\Calculators\InsuranceOutput;
use IjiUtils\MedicalInsurance\Calculators\Output;
use IjiUtils\MedicalInsurance\Calculators\OutputInterface;
use IjiUtils\MedicalInsurance\Insurance;

class BurdenBreakdownTreeGenerator
{
    /**
     * @param iterable<InsuranceOutput> $calculationOutputs
     * @return Node ルートノードを返します
     */
    public function generate(iterable $calculationOutputs): Node
    {
        $currentNode = null;

        foreach ($calculationOutputs as $insuranceOutput) {
            foreach ($insuranceOutput as $output) {
                $currentNode = $this->generateNodeFromOutput($output, $insuranceOutput->getInsurance(), $currentNode);
            }
        }

        return $currentNode->getRoot();
    }

    public function generateNodeFromOutput(Output $output, Insurance $insurance, ?Node $upperNode = null): Node
    {
        $left        = null;
        $right       = null;
        $upperNode ??= new Node(
            label:       '医療費総額',
            description: null,
            insurance:   $insurance,
            amount:      $output->getTargetAmount()
        );

        if ($output->isBenefited()) {
            $right = new Node(
                label:       (string)$output->getBenefit()->getBurdenSummary(),
                description: $output->getBenefit()->getInsurerBurdenDescription(),
                category:    $output->getCategory(),
                insurance:   $insurance,
                amount:      $output->getBenefitedAmount()
            );
            $left  = new Node(
                label:       (string)$output->getBenefit()->getBurdenSummary(),
                description: $output->getBenefit()->getPatientBurdenDescription(),
                category:    $output->getCategory(),
                insurance:   $insurance,
                amount:      $output->getBurdenAmount()
            );
            $upperNode->addRight($right);
            $upperNode->addLeft($left);
        }

        return $left ?? $upperNode;
    }
}
