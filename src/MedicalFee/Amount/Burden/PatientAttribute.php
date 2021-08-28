<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden;

use DateTimeInterface;

class PatientAttribute
{
    private ?DateTimeInterface $birthDate;
    private ?int $age;
    private bool $isElderly;

    public function __construct(
        ?DateTimeInterface $birthDate,
        ?int $age,
        bool $isElderly
    ) {
        $this->birthDate = $birthDate;
        $this->age       = $age;
        $this->isElderly = $isElderly;
    }

    public function calculateAge(DateTimeInterface $standardDate): ?int
    {
        if (!is_null($this->age)) {
            return $this->age;
        }

        return $this->birthDate
            ? $standardDate->diff($this->birthDate)->y
            : null;
    }

    public function isElderly(): bool
    {
        return $this->isElderly;
    }
}
