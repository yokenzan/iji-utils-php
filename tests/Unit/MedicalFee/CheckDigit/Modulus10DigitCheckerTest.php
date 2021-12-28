<?php

declare(strict_types=1);

namespace Tests\Unit\MedicalFee\CheckDigit;

use IjiUtils\MedicalFee\CheckDigit\Modulus10DigitChecker;
use PHPUnit\Framework\TestCase;

class Modulus10DigitCheckerTest extends TestCase
{
    private Modulus10DigitChecker $digitChecker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->digitChecker = new Modulus10DigitChecker();
    }

    /**
     * @dataProvider provideValidDigitsNumbers
     */
    public function testReturnsNoValuesWhenVerifyValid8DigitsNumber(string $validDigits)
    {
        $this->assertNull($this->digitChecker->verify($validDigits));
    }

    /**
     * @dataProvider provideValidDigitsNumbers
     */
    public function testReturnsTrueValuesWhenVerifyValid8DigitsNumber(string $validDigits)
    {
        $this->assertTrue($this->digitChecker->isValid($validDigits));
    }

    /**
     * @dataProvider provideDigitsNumbersLackingBottomDigits
     */
    public function testReturnsPossibleBottomDigitWhenIncompletedDigitsGiven(
        string $incompletedDigits,
        string $completedDigit
    ) {
        $this->assertSame($completedDigit, $this->digitChecker->calculateBottomDigit($incompletedDigits));
    }

    /**
     * @dataProvider provideInvalidDigitsNumbers
     */
    public function testThrowsExceptionValuesWhenVerifyInvalid8DigitsNumber(string $invalidDigits)
    {
        $this->expectException(\Exception::class);

        $this->digitChecker->verify($invalidDigits);
    }

    public function provideValidDigitsNumbers()
    {
        return [
            ['01010016'],
            ['01130012'],
            ['01200013'],
            ['39016019'],
            ['39136015'],
            ['54016019'],
            ['12016010'],
            ['010108'],
        ];
    }

    public function provideDigitsNumbersLackingBottomDigits()
    {
        return [
            ['0101001', '6'],
            ['0113001', '2'],
            ['0120001', '3'],
            ['3901601', '9'],
            ['3913601', '5'],
            ['5401601', '9'],
            ['1201601', '0'],
            ['01010',   '8'],
        ];
    }

    public function provideInvalidDigitsNumbers()
    {
        return [
            ['01010011'],
            ['01130011'],
            ['01200011'],
            ['39016011'],
            ['39136011'],
            ['54016011'],
            ['12016011'],
            ['010101'],
        ];
    }
}
