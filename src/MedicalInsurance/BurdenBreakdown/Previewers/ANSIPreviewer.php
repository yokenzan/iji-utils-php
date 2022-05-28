<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BurdenBreakdown\Previewers;

use IjiUtils\MedicalInsurance\BurdenBreakdown\Node;

class ANSIPreviewer implements PreviewerInterface
{
    /**
     * {@inheritDoc}
     * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
     */
    public function preview(Node $node, int $depth = 0, bool $isRight = true): string
    {
        $text = '';

        if ($depth === 0) {
            $text .= $this->previewHeader($node);
            $text .= str_repeat("\n", 3);
        }

        if ($depth > 0) {
            $text .= "\e[38:5:244m";
            if ($isRight) {
                $text .= "\e(0";
                $text .= ' qqq';
                $text .= 'qwqqqqq';
                $text .= "\e(B";
            } else {
                $text .= str_repeat(' ', 4);
                $text .= "\e(0";
                $text .= ' mqqqqq';
                $text .= "\e(B";
            }
        }

        if ($depth === 0) {
            $topLabel = sprintf('[%s]', $node->getLabel());
            $text    .= sprintf(" \e[32m%s%s\e[0m", $topLabel, str_repeat(' ', 15 - $this->halfWidthStrlen($topLabel)));
        } elseif (!$isRight) {
            $description = sprintf('[%s]', $node->getDescription());
            $text       .= sprintf(
                " \e[32m%s\e[0m\e[38:5:244m\e(0%s\e(B  ",
                $description,
                str_repeat('q', 15 - 2 - $this->halfWidthStrlen($description))
            );
        } else {
            $text .= "\e[38:5:244m";
            $text .= "\e(0";
            $text .= str_repeat('q', 14);
            $text .= str_repeat(' ', 2);
            $text .= "\e(B";
        }

        $text .= "\e[33m";
        if (!$isRight && $node->isLeaf()) {
            $text .= "\e[1;3;4:3;58:5:198m";
        }
        $text .= sprintf('%8s', $node->getAmount());
        $text .= "\e[0m";

        if ($right = $node->getRight()) {
            $text .= $this->preview($right, $depth + 1);
        }

        if ($left = $node->getLeft()) {
            $text .= "\n";
            $text .= str_repeat(' ', 16 + 8 + 4);
            for ($i = 1; $i < $depth + 1; $i++) {
                $text .= str_repeat(' ', 7);
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
                $text .= str_repeat(' ', 8);
                $text .= str_repeat(' ', 4);
                $text .= str_repeat(' ', 16);
            }
            $text .= $this->preview($left, $depth + 1, false);
        }

        if (!$isRight && $node->isLeaf()) {
            $text .= str_repeat("\n", 3);
            $text .= $this->previewHeader($node->getRoot());
        }

        return $text;
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

    private function previewHeader(Node $node): string
    {
        $padder = str_repeat('q', 8);
        $text   = sprintf("\e(0%s%s%s%s\e(B", str_repeat(' ', 6), 't', str_repeat('q', -7 + 16 + 8), $padder);

        while ($node = $node->getLeft()) {
            $category = $node->getCategory();
            $label    = sprintf('[%s]', $category->isKogaku() ? $category : $node->getLabel());
            $text    .= sprintf(
                " \e[36m%s \e[0m\e(0%s\e(B",
                $label,
                str_repeat('q', 14 - $this->halfWidthStrlen($label))
            );
            $text    .= "\e(0";
            $text    .= str_repeat('q', 7);
            if ($node->isLeaf()) {
                $text .= 'u';
            } else {
                $text .= 'n';
                $text .= str_repeat('q', 8 - 1);
                $text .= str_repeat('q', 4);
            }
            $text .= "\e(B";
        }

        return $text;
    }
}
