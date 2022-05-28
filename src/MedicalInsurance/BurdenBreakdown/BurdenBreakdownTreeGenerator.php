<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BurdenBreakdown;

use IjiUtils\MedicalInsurance\Calculators\Output;

class BurdenBreakdownTreeGenerator
{
    /**
     * @param iterable<Output> $calculationOutputs
     * @return Node ルートノードを返します
     */
    public function generate(iterable $calculationOutputs): Node
    {
        $currentNode = null;

        foreach ($calculationOutputs as $output) {
            $currentNode = $this->generateNodeFromOutput($output, $currentNode);
        }

        return $currentNode->getRoot();
    }

    public function generateNodeFromOutput(Output $output, ?Node $upperNode = null): Node
    {
        $upperNode ??= new Node(label: '医療費総額', amount: $output->getTargetAmount());
        $left        = null;
        $right       = null;

        if ($output->isBenefited()) {
            $right = new Node(
                label:       (string)$output->getBenefit()->getInsuranceDescription(),
                description: $output->getBenefit()->getInsurerBurdenDescription(),
                category:    $output->getBenefit()->getCategory(),
                amount:      $output->getBenefitedAmount()
            );
            $left  = new Node(
                label:       (string)$output->getBenefit()->getInsuranceDescription(),
                description: $output->getBenefit()->getPatientBurdenDescription(),
                category:    $output->getBenefit()->getCategory(),
                amount:      $output->getBurdenAmount()
            );
            $upperNode->addRight($right);
            $upperNode->addLeft($left);
        }

        return $left ?? $upperNode;
    }
}
