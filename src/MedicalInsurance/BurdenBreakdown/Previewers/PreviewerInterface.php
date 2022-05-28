<?php

namespace IjiUtils\MedicalInsurance\BurdenBreakdown\Previewers;

use IjiUtils\MedicalInsurance\BurdenBreakdown\Node;

interface PreviewerInterface
{
    public function preview(Node $node, int $depth = 0, bool $isRight = true): string;
}
