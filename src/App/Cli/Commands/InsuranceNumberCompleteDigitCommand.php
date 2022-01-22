<?php

declare(strict_types=1);

namespace IjiUtils\App\Cli\Commands;

use IjiUtils\MedicalFee\CheckDigit\Modulus10DigitChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InsuranceNumberCompleteDigitCommand extends Command
{
    private const INSURER_NUMBER_KOKUHO_LENGTH = 6;
    private const INSURER_NUMBER_SHAHO_LENGTH  = 8;

    /**
     * {@inheritDoc}
     */
    protected static $defaultName = 'insurance-number:complete-digit';

    private Modulus10DigitChecker $digitChecker;

    /**
     * {@inheritDoc}
     */
    public function __construct(Modulus10DigitChecker $digitChecker)
    {
        parent::__construct();
        $this->digitChecker = $digitChecker;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->addArgument('number');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $digits = $input->getArgument('number');

        if (!$this->isPossibleDigitNumber($digits)) {
            throw new \Exception('invalid argument');
        }

        $completedDigitsWithoutBottom = '';

        if (!$this->isCompletableDigitNumber($digits)) {
            $completedDigitsWithoutBottom = str_repeat('9', self::INSURER_NUMBER_SHAHO_LENGTH - strlen($digits) - 1);
        }

        $completedBomttomDigit = $this->digitChecker->calculateBottomDigit($digits . $completedDigitsWithoutBottom);
        $output->writeln(
            $this->generateOutputText($digits, $completedDigitsWithoutBottom . $completedBomttomDigit)
        );

        return Command::SUCCESS;
    }

    private function isCompletableDigitNumber(string $digits): bool
    {
        // whether consists of digits only

        if (preg_match('/^[0-9]+$/', $digits) !== 1) {
            return false;
        }

        // whether lacks 1 digit

        $digitLength      = strlen($digits);
        $lengthsCompleted = [
            self::INSURER_NUMBER_KOKUHO_LENGTH,
            self::INSURER_NUMBER_SHAHO_LENGTH,
        ];
        if (!in_array($digitLength + 1, $lengthsCompleted, true)) {
            return false;
        }

        return true;
    }

    private function isPossibleDigitNumber(string $digits): bool
    {
        return preg_match('/^[0-9]{2,7}$/', $digits) === 1;
    }

    private function generateOutputText(string $digits, string $completedDigit): string
    {
        return sprintf('%s<info>%s</info>', $digits, $completedDigit);
    }
}
