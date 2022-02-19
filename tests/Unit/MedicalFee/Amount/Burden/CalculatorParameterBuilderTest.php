<?php

declare(strict_types=1);

namespace Tests\Unit\MedicalFee\Amount\Burden;

use IjiUtils\MedicalFee\Amount\Burden\CalculatorParameterBuilder;
use IjiUtils\MedicalFee\Amount\Burden\GenerationClassification;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\ElderlyIncomeClassification;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\IncomeClassificationAttributeMaster;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\NonElderlyIncomeClassification;
use IjiUtils\MedicalFee\Point\Point;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CalculatorParameterBuilderTest extends TestCase
{
    private CalculatorParameterBuilder $builder;

    public function setUp(): void
    {
        $this->builder = new CalculatorParameterBuilder(
            new IncomeClassificationAttributeMaster(),
            $this->createMock(LoggerInterface::class)
        );
        $this->builder->clearState();
    }

    public function testA()
    {
        $this->builder->point      = Point::generate(1000);
        $this->builder->patientAge = 65;

        $this->assertEquals(
            GenerationClassification::NORMAL()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testB()
    {
        $this->builder->point      = Point::generate(1000);
        $this->builder->patientAge = 6;

        $this->assertEquals(
            GenerationClassification::PRESCHOOL()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testC()
    {
        $this->builder->point      = Point::generate(1000);
        $this->builder->patientAge = 70;

        $this->assertEquals(
            GenerationClassification::EARLY_ELDERLY()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testD()
    {
        $this->builder->point      = Point::generate(1000);
        $this->builder->patientAge = 75;

        $this->assertEquals(
            GenerationClassification::LATE_ELDERLY()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testE()
    {
        $this->builder->point      = Point::generate(1000);
        $this->builder->patientAge = 75;
        $this->builder->burden     = 0.3;

        $this->assertEquals(
            0.3,
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testF()
    {
        $this->builder->point      = Point::generate(1000);
        $this->builder->patientAge = 70;
        $this->builder->burden     = 0.3;

        $this->assertEquals(
            0.3,
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testG()
    {
        $this->builder->point      = Point::generate(1000);
        $this->builder->patientAge = 65;
        $this->builder->burden     = 0.1;

        $this->assertEquals(
            0.1,
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testH()
    {
        $this->builder->point                    = Point::generate(1000);
        $this->builder->generationClassification = GenerationClassification::LATE_ELDERLY();

        $this->assertEquals(
            GenerationClassification::LATE_ELDERLY()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testI()
    {
        $this->builder->point                    = Point::generate(1000);
        $this->builder->generationClassification = GenerationClassification::EARLY_ELDERLY();

        $this->assertEquals(
            GenerationClassification::EARLY_ELDERLY()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testJ()
    {
        $this->builder->point                    = Point::generate(1000);
        $this->builder->generationClassification = GenerationClassification::PRESCHOOL();

        $this->assertEquals(
            GenerationClassification::PRESCHOOL()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testK()
    {
        $this->builder->point                    = Point::generate(1000);
        $this->builder->generationClassification = GenerationClassification::NORMAL();

        $this->assertEquals(
            GenerationClassification::NORMAL()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testL()
    {
        foreach (NonElderlyIncomeClassification::values() as $classification) {
            $this->builder->clearState();
            $this->builder->point                   = Point::generate(1000);
            $this->builder->incomeClassificationKey = $classification->getValue();

            $this->assertEquals(
                GenerationClassification::NORMAL()->getDefaultBurdenRate(),
                $this->builder->build()->getRateBasedParameter()->getBurden()
            );
        }
    }

    public function testM()
    {
        $this->builder->clearState();
        $this->builder->point                   = Point::generate(1000);
        $this->builder->incomeClassificationKey = ElderlyIncomeClassification::UPPER_1()->getValue();

        $this->assertEquals(
            GenerationClassification::NORMAL()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testN()
    {
        $this->builder->clearState();
        $this->builder->point                   = Point::generate(1000);
        $this->builder->incomeClassificationKey = ElderlyIncomeClassification::UPPER_2()->getValue();

        $this->assertEquals(
            GenerationClassification::NORMAL()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }

    public function testO()
    {
        $this->builder->clearState();
        $this->builder->point                   = Point::generate(1000);
        $this->builder->incomeClassificationKey = ElderlyIncomeClassification::UPPER_3()->getValue();

        $this->assertEquals(
            GenerationClassification::NORMAL()->getDefaultBurdenRate(),
            $this->builder->build()->getRateBasedParameter()->getBurden()
        );
    }
}
