<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi;

use Ds\Map;
use IjiUtils\MedicalFee\Amount\Amount;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\ElderlyIncomeClassification as Elderly;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\IncomeClassificationAttribute as Attribute;
use IjiUtils\MedicalFee\Amount\Burden\KogakuRyoyohi\NonElderlyIncomeClassification as NonElderly;
use IjiUtils\MedicalFee\Nyugai;
use InvalidArgumentException;

class IncomeClassificationAttributeMaster
{
    /**
     * @property Map<string, Map<string, Attribute>> $nonElderlyIncomeClassifications
     */
    private Map $nonElderlyMaster;
    /**
     * @property Map<string, Map<string, Attribute>> $elderlyIncomeClassifications
     */
    private Map $elderlyMaster;

    public function __construct()
    {
        $this->nonElderlyMaster = new Map();
        $this->elderlyMaster    = new Map();

        $this->initializeElderlyMaster();
        $this->initializeNonElderlyMaster();
    }

    public function detect(CalculatorParameter $parameter): Attribute
    {
        if (!$parameter->hasKogaku()) {
            throw new InvalidArgumentException();
        }

        $classifications = $parameter->isElderly()
            ? $this->elderlyMaster
            : $this->nonElderlyMaster;

        return $classifications
            ->get($parameter->getNyugai()->getKey())
            ->get($parameter->getCountState()->getKey())
            ->get($parameter->getIncomeClassification()->getKey());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function findIncomeClassificationByKey(string $classificationKey): IncomeClassification
    {
        return match (true) {
            NonElderly::isValid($classificationKey) => new NonElderly($classificationKey),
            Elderly::isValid($classificationKey)    => new Elderly($classificationKey),
            default                                 => throw new InvalidArgumentException(),
        };
    }

    /**
     * 70歳未満
     */
    private function initializeNonElderlyMaster(): void
    {
        $normalParameters  = [
            ['classification' => NonElderly::A(), 'totalAmount' => 842000, 'basicAmount' => 252600],
            ['classification' => NonElderly::I(), 'totalAmount' => 558000, 'basicAmount' => 167400],
            ['classification' => NonElderly::U(), 'totalAmount' => 267000, 'basicAmount' => 80100],
            ['classification' => NonElderly::E(), 'totalAmount' => null,   'basicAmount' => 57600],
            ['classification' => NonElderly::O(), 'totalAmount' => null,   'basicAmount' => 35400],
        ];
        $reducedParameters = [
            ['classification' => NonElderly::A(), 'totalAmount' => null, 'basicAmount' => 140100],
            ['classification' => NonElderly::I(), 'totalAmount' => null, 'basicAmount' => 93000],
            ['classification' => NonElderly::U(), 'totalAmount' => null, 'basicAmount' => 44400],
            ['classification' => NonElderly::E(), 'totalAmount' => null, 'basicAmount' => 44400],
            ['classification' => NonElderly::O(), 'totalAmount' => null, 'basicAmount' => 24600],
        ];

        $nyuinKey     = Nyugai::NYUIN()->getKey();
        $normalState  = KogakuCountState::NORMAL()->getKey();
        $reducedState = KogakuCountState::REDUCED()->getKey();

        $this->nonElderlyMaster->put($nyuinKey, new Map());
        $this->nonElderlyMaster->get($nyuinKey)->put($normalState, new Map());
        $this->nonElderlyMaster->get($nyuinKey)->put($reducedState, new Map());

        $this->addIncomeClassifications($normalParameters, false, $nyuinKey, $normalState);
        $this->addIncomeClassifications($reducedParameters, false, $nyuinKey, $reducedState);

        $this->nonElderlyMaster->put(
            Nyugai::GAIRAI()->getKey(),
            $this->nonElderlyMaster->get($nyuinKey)
        );
    }

    /**
     * 70歳以上
     */
    private function initializeElderlyMaster(): void
    {
        $gairaiNormalParameters  = [
            ['classification' => Elderly::UPPER_3(), 'totalAmount' => 842000, 'basicAmount' => 252600],
            ['classification' => Elderly::UPPER_2(), 'totalAmount' => 558000, 'basicAmount' => 167400],
            ['classification' => Elderly::UPPER_1(), 'totalAmount' => 267000, 'basicAmount' => 80100],
            ['classification' => Elderly::MIDDLE(),  'totalAmount' => null,   'basicAmount' => 18000],
            ['classification' => Elderly::LOWER_2(), 'totalAmount' => null,   'basicAmount' => 8000],
            ['classification' => Elderly::LOWER_1(), 'totalAmount' => null,   'basicAmount' => 8000],
        ];
        $gairaiReducedParameters = [
            ['classification' => Elderly::UPPER_3(), 'totalAmount' => null,   'basicAmount' => 140100],
            ['classification' => Elderly::UPPER_2(), 'totalAmount' => null,   'basicAmount' => 93000],
            ['classification' => Elderly::UPPER_1(), 'totalAmount' => null,   'basicAmount' => 44400],
            ['classification' => Elderly::MIDDLE(),  'totalAmount' => null,   'basicAmount' => 44400],
            ['classification' => Elderly::LOWER_2(), 'totalAmount' => null,   'basicAmount' => 8000],
            ['classification' => Elderly::LOWER_1(), 'totalAmount' => null,   'basicAmount' => 8000],
        ];
        $nyuinNormalParameters   = [
            ['classification' => Elderly::UPPER_3(), 'totalAmount' => 842000, 'basicAmount' => 252600],
            ['classification' => Elderly::UPPER_2(), 'totalAmount' => 558000, 'basicAmount' => 167400],
            ['classification' => Elderly::UPPER_1(), 'totalAmount' => 267000, 'basicAmount' => 80100],
            ['classification' => Elderly::MIDDLE(),  'totalAmount' => null,   'basicAmount' => 57600],
            ['classification' => Elderly::LOWER_2(), 'totalAmount' => null,   'basicAmount' => 24600],
            ['classification' => Elderly::LOWER_1(), 'totalAmount' => null,   'basicAmount' => 15000],
        ];
        $nyuinReducedParameters  = [
            ['classification' => Elderly::UPPER_3(), 'totalAmount' => null,   'basicAmount' => 140100],
            ['classification' => Elderly::UPPER_2(), 'totalAmount' => null,   'basicAmount' => 93000],
            ['classification' => Elderly::UPPER_1(), 'totalAmount' => null,   'basicAmount' => 44400],
            ['classification' => Elderly::MIDDLE(),  'totalAmount' => null,   'basicAmount' => 44400],
            ['classification' => Elderly::LOWER_2(), 'totalAmount' => null,   'basicAmount' => 24600],
            ['classification' => Elderly::LOWER_1(), 'totalAmount' => null,   'basicAmount' => 15000],
        ];

        $nyuinKey     = Nyugai::NYUIN()->getKey();
        $gairaiKey    = Nyugai::GAIRAI()->getKey();
        $normalState  = KogakuCountState::NORMAL()->getKey();
        $reducedState = KogakuCountState::REDUCED()->getKey();

        $this->elderlyMaster->put($gairaiKey, new Map());
        $this->elderlyMaster->get($gairaiKey)->put($normalState, new Map());
        $this->elderlyMaster->get($gairaiKey)->put($reducedState, new Map());

        $this->addIncomeClassifications($gairaiNormalParameters, true, $gairaiKey, $normalState);
        $this->addIncomeClassifications($gairaiReducedParameters, true, $gairaiKey, $reducedState);

        $this->elderlyMaster->put($nyuinKey, new Map());
        $this->elderlyMaster->get($nyuinKey)->put($normalState, new Map());
        $this->elderlyMaster->get($nyuinKey)->put($reducedState, new Map());

        $this->addIncomeClassifications($nyuinNormalParameters, true, $nyuinKey, $normalState);
        $this->addIncomeClassifications($nyuinReducedParameters, true, $nyuinKey, $reducedState);
    }

    private function addIncomeClassifications(array $arrayOfParameters, bool $isElderly, string $nyugaiKey, string $countStateKey): void
    {
        $table = ($isElderly ? $this->elderlyMaster : $this->nonElderlyMaster)
            ->get($nyugaiKey)
            ->get($countStateKey);

        foreach ($arrayOfParameters as $parameters) {
            $table->put(
                $parameters['classification']->getKey(),
                new Attribute(
                    is_null($parameters['totalAmount']) ? null : Amount::generate($parameters['totalAmount']),
                    Amount::generate($parameters['basicAmount']),
                )
            );
        }
    }
}
