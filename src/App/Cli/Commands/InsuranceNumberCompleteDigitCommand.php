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
    private const DUMMY_DIGIT                  = '1';

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
        $givenDigits = $input->getArgument('number');

        if (is_null($givenDigits)) {
            throw new \Exception('invalid argument');
        }

        if (!$this->onlyConsistsWithNumbers($givenDigits)) {
            throw new \Exception('invalid argument');
        }

        $dummyDigits = !$this->isCompletableDigitNumber($givenDigits)
            ? $this->getDummyDigits($givenDigits)
            : '';

        $completedBomttomDigit = $this->digitChecker->calculateBottomDigit($givenDigits . $dummyDigits);

        $output->writeln(
            $this->generateOutputText($givenDigits, $dummyDigits . $completedBomttomDigit)
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

    private function onlyConsistsWithNumbers(string $digits): bool
    {
        return preg_match('/^[0-9]{2,7}$/', $digits) === 1;
    }

    private function generateOutputText(string $original, string $completed): string
    {
        return sprintf('%s<info>%s</info>', $original, $completed);
    }

    private function getDummyDigits(string $digits): string
    {
        return str_repeat(
            self::DUMMY_DIGIT,
            self::INSURER_NUMBER_SHAHO_LENGTH - strlen($digits) - 1
        );
    }
}
