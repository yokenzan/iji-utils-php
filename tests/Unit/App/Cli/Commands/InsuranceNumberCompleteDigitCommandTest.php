<?php

declare(strict_types=1);

namespace Tests\Unit\App\Cli\Commands;

use IjiUtils\App\Cli\Commands\InsuranceNumberCompleteDigitCommand;
use IjiUtils\MedicalFee\CheckDigit\Modulus10DigitChecker;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\TestCase;

class InsuranceNumberCompleteDigitCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new CommandTester(
            new InsuranceNumberCompleteDigitCommand(new Modulus10DigitChecker())
        );
    }

    private function executeTest(string $givenDigits, string $expectedInsurerNumber)
    {
        $this->command->execute(['number' => $givenDigits]);

        $output = $this->command->getDisplay();
        $this->assertEquals($expectedInsurerNumber, trim($output));
    }

    /**
     * @dataProvider provideShahoDigitsLacking1Digit
     */
    public function testShahoDigitsLacking1Digit(string $givenDigits, string $expectedInsurerNumber)
    {
        $this->executeTest($givenDigits, $expectedInsurerNumber);
    }

    /**
     * @dataProvider provideKokuhoDigitsLacking1Digit
     */
    public function testKokuhoDigitsLacking1Digit(string $givenDigits, string $expectedInsurerNumber)
    {
        $this->executeTest($givenDigits, $expectedInsurerNumber);
    }

    /**
     * @dataProvider provideShahoDigitsLackingMoreThan1Digit
     */
    public function testShahoDigitsLackingMoreThan1Digit(string $givenDigits, string $expectedInsurerNumber)
    {
        $this->executeTest($givenDigits, $expectedInsurerNumber);
    }

    public function provideShahoDigitsLacking1Digit()
    {
        return [

            // 01 協会けんぽ

            ['0101001', '01010016'],
            ['0102001', '01020015'],
            ['0103001', '01030014'],
            ['0104001', '01040013'],
            ['0105001', '01050012'],
            ['0106001', '01060011'],
            ['0107001', '01070010'],
            ['0108001', '01080019'],
            ['0109001', '01090018'],
            ['0110001', '01100015'],
            ['0111001', '01110014'],
            ['0112001', '01120013'],
            ['0113001', '01130012'],
            ['0114001', '01140011'],
            ['0120001', '01200013'],

            // 06 健保組合

            ['0613442', '06134423'],
            ['0613135', '06131353'],
            ['0640011', '06400113'],
            ['0626014', '06260145'],
            ['0613938', '06139380'],
            ['0613940', '06139406'],
            ['0613941', '06139414'],
            ['0613942', '06139422'],
            ['0625038', '06250385'],
            ['0613740', '06137400'],
            ['0613040', '06130405'],
            ['0606011', '06060115'],
            ['0608044', '06080444'],
            ['0613167', '06131676'],
            ['0614023', '06140230'],
            ['0623089', '06230890'],
            ['0627282', '06272827'],
            ['0609027', '06090278'],
            ['0640108', '06401087'],
        ];
    }

    public function provideKokuhoDigitsLacking1Digit()
    {
        return [
            ['13801', '138016'],
            ['13802', '138024'],
            ['13803', '138032'],
            ['13804', '138040'],
            ['13805', '138057'],
            ['13807', '138073'],
        ];
    }

    public function provideShahoDigitsLackingMoreThan1Digit()
    {
        return [
            ['01',  '01111111'],
            ['02',  '02111110'],
            ['06',  '06111116'],
            ['31',  '31111115'],
            ['32',  '32111114'],
            ['34',  '34111112'],
            ['39',  '39111117'],
            ['63',  '63111116'],
            ['67',  '67111112'],

            ['010', '01011113'],
            ['020', '02011112'],
            ['060', '06011118'],
            ['310', '31011117'],
            ['320', '32011116'],
            ['340', '34011114'],
            ['390', '39011119'],
            ['630', '63011118'],
            ['670', '67011114'],
        ];
    }
}
