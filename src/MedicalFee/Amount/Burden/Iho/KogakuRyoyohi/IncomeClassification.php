<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi;

interface IncomeClassification
{
    public function getName(): string;

    public function isElderly(): bool;

    public function isComparableToNonEldery(): bool;

    // phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint

    /**
     * @return string
     */
    public function getValue();

    /**
     * @return string
     */
    public function getKey();

    // phpcs:enable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
}
