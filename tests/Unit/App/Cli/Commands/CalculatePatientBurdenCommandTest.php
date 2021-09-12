<?php

declare(strict_types=1);

namespace Tests\Unit\App\Cli\Commands;

use IjiUtils\App\Cli\Commands\CalculatePatientBurdenCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\TestCase;

class CalculatePatientBurdenCommandTest extends TestCase
{
    /**
     * @dataProvider provideCalculateRateBasedBurden
     * @dataProvider provideCalculateKogakuBasedBurden
     * @dataProvider provideCalculateNyuinKogakuBasedBurden
     * @dataProvider provideCalculateElderlyBurden
     */
    public function testCalculateRateBasedBurden(int $point, array $options, int $result)
    {
        $command = new CommandTester($this->getCommand());
        $command->execute(['point' => $point] + $options);
        $this->assertStringContainsString((string)$result, $command->getDisplay());
    }

    public function provideCalculateRateBasedBurden()
    {
        return [

            // when given point only

            [
                30000,
                [],
                90000,
            ],

            // when given burden rate

            [
                30000,
                [
                    '--patient-burden-rate'   => '.3',
                ],
                90000,
            ],
            [
                30000,
                [
                    '--patient-burden-rate'   => '.2',
                ],
                60000,
            ],
            [
                30000,
                [
                    '--patient-burden-rate'   => '.1',
                ],
                30000,
            ],
            [
                30000,
                [
                    '--patient-burden-rate'   => '1',
                ],
                300000,
            ],
            [
                30000,
                [
                    '--patient-age' => '2',
                ],
                60000,
            ],
            [
                30000,
                [
                    '--patient-age' => '7',
                ],
                90000,
            ],
            [
                30000,
                [
                    '--patient-age' => '69',
                ],
                90000,
            ],

            // when given birth date

            [
                30000,
                [
                    '--patient-birth-date' => '1987-07-28',
                ],
                90000,
            ],

            [
                30000,
                [
                    '--patient-birth-date' => '2020-01-01',
                ],
                60000,
            ],
        ];
    }

    public function provideCalculateKogakuBasedBurden()
    {
        return [
            [
                100000,
                [
                    '--kogaku-classification' => 'a',
                ],
                254180,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'a',
                    '--kogaku-is-reduced'     => true,
                ],
                140100,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'i',
                ],
                171820,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'i',
                    '--kogaku-is-reduced'     => true,
                ],
                93000,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'u',
                ],
                87430,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'u',
                    '--kogaku-is-reduced'     => true,
                ],
                44400,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'e',
                ],
                57600,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'e',
                    '--kogaku-is-reduced'     => true,
                ],
                44400,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'o',
                ],
                35400,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'o',
                    '--kogaku-is-reduced'     => true,
                ],
                24600,
            ],
        ];
    }

    public function provideCalculateNyuinKogakuBasedBurden()
    {
        return [
            [
                100000,
                [
                    '--kogaku-classification' => 'a',
                    '--receipt-is-nyuin'      => true,
                ],
                254180,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'a',
                    '--kogaku-is-reduced'     => true,
                    '--receipt-is-nyuin'      => true,
                ],
                140100,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'i',
                    '--receipt-is-nyuin'      => true,
                ],
                171820,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'i',
                    '--kogaku-is-reduced'     => true,
                    '--receipt-is-nyuin'      => true,
                ],
                93000,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'u',
                    '--receipt-is-nyuin'      => true,
                ],
                87430,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'u',
                    '--kogaku-is-reduced'     => true,
                    '--receipt-is-nyuin'      => true,
                ],
                44400,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'e',
                    '--receipt-is-nyuin'      => true,
                ],
                57600,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'e',
                    '--kogaku-is-reduced'     => true,
                    '--receipt-is-nyuin'      => true,
                ],
                44400,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'o',
                    '--receipt-is-nyuin'      => true,
                ],
                35400,
            ],
            [
                100000,
                [
                    '--kogaku-classification' => 'o',
                    '--kogaku-is-reduced'     => true,
                    '--receipt-is-nyuin'      => true,
                ],
                24600,
            ],
        ];
    }

    public function provideCalculateElderlyBurden()
    {
        return [
            [
                3000,
                [
                    '--patient-age' => 70,
                ],
                6000,
            ],
            [
                3000,
                [
                    '--patient-age' => 74,
                ],
                6000,
            ],
            [
                3000,
                [
                    '--patient-age' => 75,
                ],
                3000,
            ],

            // by generation

            [
                3000,
                [
                    '--patient-is-early-elderly' => true,
                ],
                6000,
            ],
            [
                3000,
                [
                    '--patient-is-late-elderly' => true,
                ],
                3000,
            ],

            // 外に出す

            [
                3000,
                [
                    '--patient-is-preschool' => true,
                ],
                6000,
            ],
            [
                3000,
                [
                    '--patient-is-preschool'    => true,
                    '--patient-is-late-elderly' => true,
                ],
                3000,
            ],
            [
                3000,
                [
                    '--patient-is-early-elderly' => true,
                    '--patient-is-late-elderly'  => true,
                ],
                3000,
            ],
        ];
    }

    protected function getCommand(): Command
    {
        return self::$container->get(CalculatePatientBurdenCommand::class);
    }
}
