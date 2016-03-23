<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Calculator\BuildingInterface;

class UpgradeService
{
    protected $instance;
    protected $building;
    protected $wall;
    protected $floors;

    protected $presentStyrofoamLambda = 0.035;

    public function __construct(InstanceService $instance, BuildingInterface $building, FloorsService $floors)
    {
        $this->instance = $instance;
        $this->building = $building;
        $this->floors = $floors;
    }

    public function getVariants()
    {
        $variants = array();
        $actualEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();
        $originalCalculation = $this->instance->get();

        $this->tryBetterWallIsolation($originalCalculation, $actualEnergyLoss, $variants);
        $this->tryBetterRoofIsolation($originalCalculation, $actualEnergyLoss, $variants);
        $this->tryNewWindows($originalCalculation, $actualEnergyLoss, $variants);
        $this->tryNewDoors($originalCalculation, $actualEnergyLoss, $variants);
        $this->tryBetterGroundFloorCeilingIsolation($originalCalculation, $actualEnergyLoss, $variants);
        $this->tryBetterGroundFloorIsolation($originalCalculation, $actualEnergyLoss, $variants);
        $this->tryMechanicalVentilation($originalCalculation, $actualEnergyLoss, $variants);

        $apartment = $originalCalculation->getHouse()->getApartment();

        if ($apartment) {
            $this->tryApartmentFloorIsolation($originalCalculation, $apartment, $actualEnergyLoss, $variants);
            $this->tryApartmentCeilingIsolation($originalCalculation, $apartment, $actualEnergyLoss, $variants);
            $this->tryApartmentWallsIsolation($originalCalculation, $apartment, $actualEnergyLoss, $variants);
        }

        $gain = array();
        foreach ($variants as $key => $row) {
            if ($row['gain'] < 0.05) {
                unset($variants[$key]);
                continue;
            }

            $gain[$key] = $row['gain'];
        }

        array_multisort($gain, SORT_DESC, $variants);

        return $variants;
    }

    protected function tryApartmentWallsIsolation($originalCalculation, $apartment, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();

        if ($apartment->getNumberUnheatedWalls() > 0) {
            $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated(true);

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => 'ocieplenie ścian od pomieszczeń nieogrzewanych 5cm styropianu',
            );
        }
    }

    protected function tryApartmentCeilingIsolation($originalCalculation, $apartment, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();

        if ($apartment->getWhatsOver() != 'heated_room') {
            $ceilingIsolation = $house->getHighestCeilingIsolationLayer();

            if (!$ceilingIsolation || $ceilingIsolation->getSize() <= 5) {
                $m = new Material();
                $m->setLambda($this->presentStyrofoamLambda);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(10);

                $house->setHighestCeilingIsolationLayer($l);

                $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie sufitu 10cm styropianu',
                );
            }
        }
    }

    protected function tryApartmentFloorIsolation($originalCalculation, $apartment, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();

        if ($apartment->getWhatsUnder() != 'heated_room') {
            $ceilingIsolation = $house->getBottomIsolationLayer();

            if (!$ceilingIsolation || $ceilingIsolation->getSize() <= 5) {
                $m = new Material();
                $m->setLambda($this->presentStyrofoamLambda);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(10);

                $house->setBottomIsolationLayer($l);

                $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie podłogi 10cm styropianu',
                );
            }
        }
    }

    protected function tryBetterGroundFloorIsolation($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();

        if ($this->floors->isGroundFloorHeated()) {
            $groundFloorIsolation = $house->getBottomIsolationLayer();
            $materialName = $groundFloorIsolation && $groundFloorIsolation->getMaterial() ? $groundFloorIsolation->getMaterial()->getName() : '';

            if (!$groundFloorIsolation || $groundFloorIsolation->getSize() <= 10 || (!stristr($materialName, 'styropian') && !stristr($materialName, 'wełna'))) {
                $m = new Material();
                $m->setLambda($this->presentStyrofoamLambda);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(20);

                $house->setBottomIsolationLayer($l);

                $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie podłogi parteru 20cm styropianu',
                );
            }
        }
    }

    protected function tryBetterGroundFloorCeilingIsolation($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();

        if (!$this->floors->isGroundFloorHeated()) {
            $ceilingIsolation = $house->getBottomIsolationLayer();

            if (!$ceilingIsolation || $ceilingIsolation->getSize() <= 5) {
                $m = new Material();
                $m->setLambda($this->presentStyrofoamLambda);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(10);

                $house->setBottomIsolationLayer($l);

                $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie stropu nad parterem 10cm styropianu',
                );
            }
        }
    }

    protected function tryNewDoors($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();
        $doorsType = $house->getDoorsType();

        if (stristr($doorsType, 'old')) {
            $house->setDoorsType('new_wood');

            $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => 'wymiana drzwi',
            );
        }
    }

    protected function tryNewWindows($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();
        $windowsType = $house->getWindowsType();

        if (stristr($windowsType, 'old')) {
            $house->setWindowsType('new_double_glass');

            $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => 'wymiana wszystkich okien',
            );
        }
    }

    protected function tryBetterWallIsolation($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);

        if (!$originalCalculation->getHouse()->getExternalIsolationLayer() || $originalCalculation->getHouse()->getExternalIsolationLayer()->getSize() < 10) {
            $isolationSize = 15;

            $m = new Material();
            $m->setLambda($this->presentStyrofoamLambda);

            $l = new Layer();
            $l->setMaterial($m);
            $l->setSize($isolationSize);

            $customCalculation->getHouse()->setExternalIsolationLayer($l);

            $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => sprintf('ocieplenie ścian zewn. %scm styropianu, lambda %s', $isolationSize, $this->presentStyrofoamLambda),
            );
        }
    }

    protected function tryBetterRoofIsolation($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        if ($this->instance->get()->isApartment()) {
            return;
        }

        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();
        $roofType = $house->getBuildingRoof();

        $flatRoof = $roofType == 'flat' || $roofType == false;

        $roofIsolation = $house->getTopIsolationLayer();
        $materialName = $roofIsolation && $roofIsolation->getMaterial() ? $roofIsolation->getMaterial()->getName() : '';

        if (!$roofIsolation || $roofIsolation->getSize() < 20 || (!stristr($materialName, 'styropian') && !stristr($materialName, 'wełna'))) {
            $isolationSize = $flatRoof ? 35 : 20;

            $m = new Material();
            $m->setLambda($this->presentStyrofoamLambda);

            $l = new Layer();
            $l->setMaterial($m);
            $l->setSize($isolationSize);

            $house->setTopIsolationLayer($l);

            $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => sprintf('ocieplenie dachu %scm styropianu', $isolationSize),
            );
        }
    }

    protected function tryMechanicalVentilation($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);

        $customCalculation->getHouse()->setVentilationType('mechanical_recovery');
        $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

        $variants[] = array(
            'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
            'title' => 'wentylacja mechaniczna z odzyskiem ciepła',
        );
    }
}
