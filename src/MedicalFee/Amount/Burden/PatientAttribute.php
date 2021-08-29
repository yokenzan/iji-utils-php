<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden;

use DateTimeInterface;

class PatientAttribute
{
    private ?DateTimeInterface       $birthDate;
    private ?int                     $age;
    private GenerationClassification $generationClassification;

    public function __construct(
        ?DateTimeInterface       $birthDate,
        ?int                     $age,
        GenerationClassification $generationClassification
    ) {
        $this->birthDate                = $birthDate;
        $this->age                      = $age;
        $this->generationClassification = $generationClassification;
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
        return $this->generationClassification->isElderly();
    }
}
