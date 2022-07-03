<?php

declare(strict_types=1);

namespace IjiUtils\App\Cli\Commands;

use Ds\Vector;
use IjiUtils\MedicalFee\Amount\Burden\Iho\KogakuRyoyohi\NonElderlyIncomeClassification;
use IjiUtils\MedicalFee\Nyugai;
use IjiUtils\MedicalFee\ValueObjects\Point;
use IjiUtils\MedicalInsurance\BenefitWays\KogakuBenefitWay;
use IjiUtils\MedicalInsurance\BenefitWays\LimitBenefitWay;
use IjiUtils\MedicalInsurance\BenefitWays\RateBenefitWay;
use IjiUtils\MedicalInsurance\BurdenBreakdown\BurdenBreakdownTreeGenerator;
use IjiUtils\MedicalInsurance\BurdenBreakdown\Previewers\ANSIPreviewer;
use IjiUtils\MedicalInsurance\Calculators\Calculator;
use IjiUtils\MedicalInsurance\Calculators\Input;
use IjiUtils\MedicalInsurance\Insurance;
use IjiUtils\MedicalInsurance\InsurerType;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;
use IjiUtils\MedicalInsurance\ValueObjects\BurdenRate;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 患者自己負担額を計算するコマンド
 */
class DummyCalculatePatientBurdenCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected static $defaultName = 'dummy:calc:burden';

    private Calculator                   $calculator;
    private BurdenBreakdownTreeGenerator $treeGenerator;
    private LoggerInterface              $logger;
    private ANSIPreviewer                $treePreviewer;

    public function __construct(
        Calculator                   $calculator,
        BurdenBreakdownTreeGenerator $treeGenerator,
        LoggerInterface              $logger,
        ANSIPreviewer                $treePreviewer
    ) {
        parent::__construct();

        $this->treeGenerator = $treeGenerator;
        $this->calculator    = $calculator;
        $this->logger        = $logger;
        $this->treePreviewer = $treePreviewer;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->addArgument('point', InputArgument::REQUIRED, '請求点数を指定します。');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /*
         * 保険情報の生成
         */

        $insurances = new Vector();
        $insurances->push(new Insurance(
            insurerType:   InsurerType::IHO(),
            description:   '01協会けんぽ',
            rateBenefit:   new RateBenefitWay(BurdenRate::generate(0.3)),
            kogakuBenefit: new KogakuBenefitWay(NonElderlyIncomeClassification::U()),
            limitBenefit:  new LimitBenefitWay(Amount::generate(20000))
        ));
        $insurances->push(new Insurance(
            insurerType:  InsurerType::KOHI(),
            description:  '54指定難病',
            rateBenefit:  new RateBenefitWay(BurdenRate::generate(0.2)),
            limitBenefit: new LimitBenefitWay(Amount::generate(10000))
        ));
        $insurances->push(new Insurance(
            insurerType:  InsurerType::KOHI(),
            description:  '81重度身障',
            rateBenefit:  new RateBenefitWay(BurdenRate::generate(0.1)),
            limitBenefit: new LimitBenefitWay(Amount::generate(5000))
        ));

        /*
         * 保険情報の出力
         */

        $countKohi = 0;

        /** @var Insurance $insurance */
        foreach ($insurances as $insurance) {
            $table       = new Table($output);
            $insurerType = $insurance->getInsurerType();
            $isKohi      = $insurerType->equals(InsurerType::KOHI());
            $title       = sprintf('%s%s%s', $isKohi ? '第' : '', $isKohi ? ++$countKohi : '', $insurerType);
            $table
                ->setColumnWidth(0, 12)
                ->setColumnWidth(1, 16)
                ->setStyle('box-double')
                ->setHeaders([$title, $insurance->getDescription()])
            ;

            foreach ($insurance->__toBenefits() as $benefit) {
                $table->addRow([$benefit->getCategory(), $benefit->getBurdenSummary()]);
            }

            $table->render();
        }

        $this->br($output);

        /*
         * 入力パラメタ(請求点数や入外など)
         */

        $calculationInput = new Input(
            point:  Point::generate((int)$input->getArgument('point')),
            nyugai: Nyugai::GAIRAI()
        );

        $table = new Table($output);
        $table
            ->setColumnWidth(0, 12)
            ->setColumnWidth(1, 16)
            ->setStyle('box-double')
            ->setHeaderTitle('入力パラメタ')
            ->setHeaders(['項目', '入力値'])
            ->addRows([
                ['入外',     $calculationInput->getNyugai()],
                ['請求点数', $calculationInput->getPoint() . '点'],
            ])
            ->render()
        ;

        $this->br($output);

        /*
         * 計算処理
         */

        $results = $this->calculator->calculate($insurances, $calculationInput);

        /*
         * 給付樹形図の出力
         */

        $tree = $this->treeGenerator->generate($results);
        $output->writeln($this->treePreviewer->preview($tree));

        $this->br($output);

        return Command::SUCCESS;
    }

    private function br(OutputInterface $output): void
    {
        $output->writeln((new Vector(range(1, 2)))->map(fn ($_) => ''));
    }
}
