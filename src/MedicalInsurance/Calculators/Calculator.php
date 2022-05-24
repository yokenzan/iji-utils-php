<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\Calculators;

use Ds\Vector;
use IjiUtils\MedicalInsurance\Insurance;

class Calculator
{
    /**
     * @param Vector<Insurance> $insurances
     * @return Vector<Output>
     */
    public function calculate(iterable $insurances, Input $input): iterable
    {
        $benefitsFromInsurances = $insurances->map(fn (Insurance $insurance) => $insurance->toBenefits());
        $flattenBenefits        = new Vector();

        foreach ($benefitsFromInsurances as $benefits) {
            foreach ($benefits as $benefit) {
                $flattenBenefits->push($benefit);
            }
        }

        $inputFromUpper = $input;
        $outputs        = new Vector();

        /** @var \IjiUtils\MedicalInsurance\AppliedBenefit $benefit */
        foreach ($flattenBenefits as $benefit) {
            $output         = $benefit->calculate($inputFromUpper);
            $inputFromUpper = $this->generateInputFromOutput($output);
            $outputs->push($output);
        }

        return $outputs;
    }

    public function generateInputFromOutput(Output $upperOutput): Input
    {
        $upperInput = $upperOutput->getInput();

        return new Input(
            point:        $upperInput->getPoint(),
            nyugai:       $upperInput->getNyugai(),
            targetAmount: $upperOutput->getBurdenAmount()
        );
    }
}
