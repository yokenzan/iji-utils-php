<?php

declare(strict_types=1);

namespace Tests\Unit\App\Cli\Commands;

use IjiUtils\App\Cli\Commands\ShowIncomeClassificationsCommand;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\ElderlyIncomeClassification;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\NonElderlyIncomeClassification;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ShowIncomeClassificationsCommandTest extends TestCase
{
    public function testExecute()
    {
        $command = new CommandTester(new ShowIncomeClassificationsCommand());
        $command->execute([]);
        $output = $command->getDisplay();

        foreach (NonElderlyIncomeClassification::values() as $value) {
            $this->assertStringContainsString($value->getName(), $output);
        }

        foreach (ElderlyIncomeClassification::values() as $value) {
            $this->assertStringContainsString($value->getName(), $output);
        }
    }
}
