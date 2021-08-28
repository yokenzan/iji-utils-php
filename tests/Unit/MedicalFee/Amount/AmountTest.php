<?php

declare(strict_types=1);

namespace Tests\Unit\MedicalFee\Amount;

use IjiUtils\MedicalFee\Amount\Amount;
use IjiUtils\MedicalFee\Point\Point;
use PHPUnit\Framework\TestCase;

class AmountTest extends TestCase
{
    public function testCanGenerateNewInstance()
    {
        $this->assertInstanceOf(Amount::class, new Amount(100));
    }

    public function testCanGenerateNewInstanceByStaticMethod()
    {
        $this->assertEquals(new Amount(100), Amount::generate(100));
    }

    public function testIsStringable()
    {
        $this->assertEquals('1,000', (string)Amount::generate(1000));
    }

    public function testCanConvertIntoInteger()
    {
        $this->assertEquals(1000, Amount::generate(1000)->toInt());
    }

    public function testInternalIntegerValueCanConvertIntoFloat()
    {
        $this->assertEquals(1000.0, Amount::generate(1000)->toFloat());
    }

    public function testIntegerValueIsntChangedWhenRounded()
    {
        $this->assertEquals(Amount::generate(1000), Amount::generate(1000)->round());
        $this->assertEquals(Amount::generate(2000), Amount::generate(2000)->round());
    }

    public function testGeneratesNewInstanceWhenRounds()
    {
        $original = Amount::generate(1000);
        $rounded  = $original->round();
        $this->assertNotSame($original, $rounded);
    }

    public function testCanGenerateNewInstanceWithFloatValue()
    {
        $this->assertInstanceOf(Amount::class, new Amount(100.8));
    }

    public function testCanGenerateNewInstanceWithFloatValueByStaticMethod()
    {
        $this->assertInstanceOf(Amount::class, new Amount(100.8));
    }

    public function testFloatValueIsHalfUppedWhenRounded()
    {
        $this->assertEquals(Amount::generate(1000), Amount::generate(1000.4)->round());
        $this->assertEquals(Amount::generate(1001), Amount::generate(1000.5)->round());
    }

    public function testFloatValueCanConvertIntoIntegerWithHalfUpped()
    {
        $this->assertEquals(1000, Amount::generate(1000.4)->toInt());
        $this->assertEquals(1001, Amount::generate(1000.5)->toInt());
    }

    public function testFloatValueIsStringableFloatValueWithHalfUpped()
    {
        $this->assertEquals('1,000', (string)Amount::generate(1000.4));
        $this->assertEquals('1,001', (string)Amount::generate(1000.5));
    }

    public function testInternalFloatValueIsNotChangedWhenConvertedIntoFloat()
    {
        $this->assertEquals(1000.9, Amount::generate(1000.9)->toFloat());
    }

    public function testGeneratesNewInstanceWhenRoundsFloatValue()
    {
        $original = Amount::generate(1000.4);
        $rounded  = $original->round();

        $this->assertNotSame($original, $rounded);
    }

    public function testPointValueIsMultipliedBy10WhenCalculateAmountFromPointWithoutRate()
    {
        $pointValue = 100;
        $point      = Point::generate($pointValue);
        $amount     = Amount::fromPoint($point);

        $this->assertEquals(Amount::generate($pointValue * 10), $amount);
    }

    public function testPointValueIsMultipliedBySpecifiedRateWhenCalculateAmountFromPoint()
    {
        $pointValue = 100;
        $burdenRate = 0.3;
        $point      = Point::generate($pointValue);
        $amount     = Amount::fromPoint($point, $burdenRate);

        $this->assertEquals(Amount::generate($pointValue * 10 * $burdenRate), $amount);
    }
}
