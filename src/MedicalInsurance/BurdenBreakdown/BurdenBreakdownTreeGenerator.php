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
            $currentNode = $this->generateTree($output, $currentNode);
        }

        return $currentNode->getRoot();
    }

    public function stringify(Node $node, int $depth = 0, bool $isRight = true): string
    {
        $text = '';

        if ($depth > 0) {
            $text .= "\e[38:5:244m";
            if ($isRight) {
                $text .= "\e(0";
                $text .= ' qqq';
                $text .= "\e(B";
                $label = sprintf('[%s]', $node->getLabel());
                $text .= sprintf("\e[36m%s\e[0m\e[38:5:244m\e(0%s\e(B", $label, str_repeat('q', 10 - $this->halfWidthStrlen($label)));
                $text .= "\e(0";
                $text .= 'qwqqqqq';
                $text .= "\e(B";
            } else {
                $text .= str_repeat(' ', 4);
                $text .= str_repeat(' ', 10);
                $text .= "\e(0";
                $text .= ' mqqqqq';
                $text .= "\e(B";
            }
            $text .= "\e[0m";
        }

        if ($depth === 0) {
            $topLabel = sprintf('[%s]', $node->getLabel());
            $text    .= sprintf(" \e[32m%s%s\e[0m", $topLabel, str_repeat(' ', 15 - $this->halfWidthStrlen($topLabel)));
        } elseif (!$isRight) {
            $description = sprintf('[%s]', $node->getDescription());
            $text       .= sprintf(" \e[32m%s%s\e[0m", $description, str_repeat(' ', 15 - $this->halfWidthStrlen($description)));
        } else {
            $text .= "\e[38:5:244m";
            $text .= "\e(0";
            $text .= str_repeat('q', 14);
            $text .= str_repeat(' ', 2);
            $text .= "\e(B";
        }

        $text .= "\e[33m";
        $text .= sprintf('%8s', $node->getAmount());
        $text .= "\e[0m";

        if ($right = $node->getRight()) {
            $text .= $this->stringify($right, $depth + 1);
        }

        if ($left = $node->getLeft()) {
            $text .= "\n";
            $text .= str_repeat(' ', 16 + 8 + 4 + 10);
            for ($i = 1; $i < $depth + 1; $i++) {
                $text .= str_repeat(' ', 7);
                $text .= str_repeat(' ', 10);
                $text .= str_repeat(' ', 8);
                $text .= str_repeat(' ', 4);
                $text .= str_repeat(' ', 16);
            }
            $text .= "\e[38:5:244m";
            $text .= "\e(0";
            $text .= ' x';
            $text .= "\e(B";
            $text .= "\e[0m";

            $text .= "\n";
            $text .= str_repeat(' ', 16 + 8);
            for ($i = 1; $i < $depth + 1; $i++) {
                $text .= str_repeat(' ', 7);
                $text .= str_repeat(' ', 10);
                $text .= str_repeat(' ', 8);
                $text .= str_repeat(' ', 4);
                $text .= str_repeat(' ', 16);
            }
            $text .= $this->stringify($left, $depth + 1, false);
        }

        return $text;
    }

    private function generateTree(Output $output, ?Node $upperNode = null): Node
    {
        $upperNode ??= new Node(label: '総額', amount: $output->getTargetAmount());
        $left        = null;
        $right       = null;

        if ($output->isBenefited()) {
            $right = new Node(
                label:       (string)$output->getBenefit()->getInsurerType(),
                description: $output->getBenefit()->getInsurerBurdenDescription(),
                amount:      $output->getBenefitedAmount()
            );
            $left  = new Node(
                label:       (string)$output->getBenefit()->getInsurerType(),
                description: $output->getBenefit()->getPatientBurdenDescription(),
                amount:      $output->getBurdenAmount()
            );
            $upperNode->addRight($right);
            $upperNode->addLeft($left);
        }

        return $left ?? $upperNode;
    }

    /**
     * UTF-8のみ
     */
    private function halfWidthStrlen(string $string): int
    {
        $a = mb_strlen($string);
        $b = strlen($string);
        return $a - ($b - $a) / 2 + ($b - $a);
    }
}
