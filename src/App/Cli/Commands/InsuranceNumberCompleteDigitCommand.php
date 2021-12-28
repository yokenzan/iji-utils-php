<?php

declare(strict_types=1);

namespace IjiUtils\App\Cli\Commands;

use IjiUtils\MedicalFee\CheckDigit\Modulus10DigitChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InsuranceNumberCompleteDigitCommand extends Command
{
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

        if (!$this->isPossibleInsuranceDigitNumber($digits)) {
            throw new \Exception('invalid argument');
        }

        $output->writeln($this->digitChecker->completeDigit($digits));

        return Command::SUCCESS;
    }

    private function isPossibleInsuranceDigitNumber(string $digits): bool
    {
        return preg_match('/^[0-9]{5,8}$/', $digits) === 1;
    }
}
