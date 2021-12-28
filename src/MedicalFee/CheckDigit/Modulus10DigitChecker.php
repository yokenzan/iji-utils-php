<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\CheckDigit;

class Modulus10DigitChecker
{
    public function isValid(string $digits): bool
    {
        $bottomLackedDigits = substr($digits, 0, strlen($digits) - 1);
        return $this->calculateBottomDigit($bottomLackedDigits) === $digits[-1];
    }

    /**
     * @throws \Exception
     */
    public function verify(string $digits): void
    {
        if (!$this->isValid($digits)) {
            throw new \Exception('invalid digit number');
        }
    }

    public function completeDigit(string $digits): string
    {
        return $digits . $this->calculateBottomDigit($digits);
    }

    private function calculateBottomDigit(string $digits): string
    {
        $bottomCoefficient      = 2;
        $nextBottomCoefficiennt = 1;
        $resultSum              = 0;
        $calculateSum           = fn (int $sum, string $element) => $sum + (int)$element;

        foreach (array_reverse(str_split($digits)) as $index => $digit) {
            $coefficient = [$bottomCoefficient, $nextBottomCoefficiennt][$index % 2];
            $multiplied  = str_split((string)((int)$digit * $coefficient));
            $result      = array_reduce($multiplied, $calculateSum, 0);
            $resultSum  += $result;
        }

        return (string)((10 - ($resultSum % 10)) % 10);
    }
}
