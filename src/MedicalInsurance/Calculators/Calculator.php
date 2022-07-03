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
        $inputFromUpper = $input;
        $outputs        = new Vector();

        foreach ($insurances as $insurance) {
            $insuranceOutput = new InsuranceOutput($inputFromUpper, $insurance);
            foreach ($insurance as $benefit) {
                $output         = $benefit->calculate($insurance, $inputFromUpper);
                $inputFromUpper = $this->generateInputFromOutput($output);
                $insuranceOutput->addChild($output);
            }
            $outputs->push($insuranceOutput);
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
