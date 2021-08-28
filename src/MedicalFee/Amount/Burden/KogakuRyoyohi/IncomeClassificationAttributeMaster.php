<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi;

use Ds\Map;
use IjiUtils\MedicalFee\Amount\Amount;

class IncomeClassificationAttributeMaster
{
    private const KOGAKU_COUNT_IS_REDUCED = 'reduced';
    private const KOGAKU_COUNT_IS_NORMAL  = 'normal';

    /**
     * @property Map<string, Map<string, IncomeClassificationAttribute>> $nonElderlyIncomeClassifications
     */
    private Map $nonElderlyIncomeClassifications;
    /**
     * @property Map<string, Map<string, IncomeClassificationAttribute>> $elderlyIncomeClassifications
     */
    private Map $elderlyIncomeClassifications;
    /**
     * @property Map<string, string> $elderlyClassificationKeys
     */
    private Map $classificationKeys;

    public function __construct()
    {
        $this->nonElderlyIncomeClassifications = new Map();
        $this->elderlyIncomeClassifications    = new Map();
        $this->classificationKeys              = new Map();

        $this->initializeElderlyIncomeClassifications();
        $this->initializeNonElderlyIncomeClassifications();
        $this->initializeClassificationKeys();
    }

    public function detect(CalculatorParameter $parameter): IncomeClassificationAttribute
    {
        $classifications = $parameter->isElderly()
            ? $this->elderlyIncomeClassifications
            : $this->nonElderlyIncomeClassifications;

        return $classifications
            ->get($parameter->getNyugai())
            ->get($parameter->isReduced() ? self::KOGAKU_COUNT_IS_REDUCED : self::KOGAKU_COUNT_IS_NORMAL)
            ->get($parameter->getIncomeClassification());
    }

    public function detectIsElderlyOrNotByClassification(string $incomeClassification): bool
    {
        return (bool)$this->classificationKeys->get($incomeClassification);
    }

    /**
     * 70歳未満
     */
    private function initializeNonElderlyIncomeClassifications(): void
    {
        // 通常回

        $this->nonElderlyIncomeClassifications->put('nyuin', new Map());
        $this->nonElderlyIncomeClassifications->get('nyuin')->put(
            self::KOGAKU_COUNT_IS_NORMAL,
            new Map([
                'a' => new IncomeClassificationAttribute(Amount::generate(842000), Amount::generate(252600)),
                'i' => new IncomeClassificationAttribute(Amount::generate(558000), Amount::generate(167400)),
                'u' => new IncomeClassificationAttribute(Amount::generate(267000), Amount::generate(80100)),
                'e' => new IncomeClassificationAttribute(null, Amount::generate(57600)),
                'o' => new IncomeClassificationAttribute(null, Amount::generate(35400)),
            ])
        );

        // 多数回

        $reducedUAndE = new IncomeClassificationAttribute(null, Amount::generate(44400));

        $this->nonElderlyIncomeClassifications->get('nyuin')->put(
            'reduced',
            new Map([
                'a' => new IncomeClassificationAttribute(null, Amount::generate(140100)),
                'i' => new IncomeClassificationAttribute(null, Amount::generate(93000)),
                'u' => $reducedUAndE,
                'e' => $reducedUAndE,
                'o' => new IncomeClassificationAttribute(null, Amount::generate(24600)),
            ])
        );

        $this->nonElderlyIncomeClassifications->put(
            'gairai',
            $this->nonElderlyIncomeClassifications->get('nyuin')
        );
    }

    /**
     * 70歳以上
     */
    private function initializeElderlyIncomeClassifications(): void
    {
        // 通常回

        $this->elderlyIncomeClassifications->put('gairai', new Map());
        $normalLower = new IncomeClassificationAttribute(null, Amount::generate(8000));

        $this->elderlyIncomeClassifications->get('gairai')->put(
            'normal',
            new Map([
                'upper-3' => new IncomeClassificationAttribute(Amount::generate(842000), Amount::generate(252600)),
                'upper-2' => new IncomeClassificationAttribute(Amount::generate(558000), Amount::generate(167400)),
                'upper-1' => new IncomeClassificationAttribute(Amount::generate(267000), Amount::generate(80100)),
                'middle'  => new IncomeClassificationAttribute(null, Amount::generate(18000)),
                'lower-2' => $normalLower,
                'lower-1' => $normalLower,
            ])
        );

        // 多数回

        $reducedMiddelAndUpper = new IncomeClassificationAttribute(null, Amount::generate(44400));

        $this->elderlyIncomeClassifications->get('gairai')->put(
            'reduced',
            new Map([
                'upper-3' => new IncomeClassificationAttribute(null, Amount::generate(140100)),
                'upper-2' => new IncomeClassificationAttribute(null, Amount::generate(93000)),
                'upper-1' => $reducedMiddelAndUpper,
                'middle'  => $reducedMiddelAndUpper,
                'lower-2' => $normalLower,
                'lower-1' => $normalLower,
            ])
        );

        $this->elderlyIncomeClassifications->put('nyuin', new Map());

        // 通常回

        $this->elderlyIncomeClassifications->get('nyuin')->put(
            'normal',
            new Map([
                'upper-3' => $this->elderlyIncomeClassifications->get('gairai')->get('normal')->get('upper-3'),
                'upper-2' => $this->elderlyIncomeClassifications->get('gairai')->get('normal')->get('upper-2'),
                'upper-1' => $this->elderlyIncomeClassifications->get('gairai')->get('normal')->get('upper-1'),
                'middle'  => new IncomeClassificationAttribute(null, Amount::generate(57600)),
                'lower-2' => new IncomeClassificationAttribute(null, Amount::generate(24600)),
                'lower-1' => new IncomeClassificationAttribute(null, Amount::generate(15000)),
            ])
        );

        // 多数回

        $reducedMiddelAndUpper = new IncomeClassificationAttribute(null, Amount::generate(44400));

        $this->elderlyIncomeClassifications->get('nyuin')->put(
            'reduced',
            new Map([
                'upper-3' => new IncomeClassificationAttribute(null, Amount::generate(140100)),
                'upper-2' => new IncomeClassificationAttribute(null, Amount::generate(93000)),
                'upper-1' => $reducedMiddelAndUpper,
                'middle'  => $reducedMiddelAndUpper,
                'lower-2' => $this->elderlyIncomeClassifications->get('nyuin')->get('normal')->get('lower-2'),
                'lower-1' => $this->elderlyIncomeClassifications->get('nyuin')->get('normal')->get('lower-1'),
            ])
        );
    }

    private function initializeClassificationKeys(): void
    {
        $elderlyClassificationKeys    = $this->elderlyIncomeClassifications
            ->get('gairai')
            ->get('normal')
            ->keys();
        $nonElderlyClassificationKeys = $this->nonElderlyIncomeClassifications
            ->get('gairai')
            ->get('normal')
            ->keys();

        foreach ($elderlyClassificationKeys as $classification) {
            $this->classificationKeys->put($classification, true);
        }
        foreach ($nonElderlyClassificationKeys as $classification) {
            $this->classificationKeys->put($classification, false);
        }
    }
}
