<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Calculator\BuildingInterface;
use Kraken\WarmBundle\Service\InstanceService;

class UpgradeService
{
    protected $instance;
    protected $building;
    protected $wall;
    protected $presentStyrofoamLambda = 0.035;

    public function __construct(InstanceService $instance, BuildingInterface $building)
    {
        $this->instance = $instance;
        $this->building = $building;
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
        foreach ($variants as $key => $row)
        {
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
                'title' => 'ocieplenie ścian od pomieszczeń nieogrzewanych 5cm styropianu'
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
                    'title' => 'ocieplenie sufitu 10cm styropianu'
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
            $ceilingIsolation = $house->getLowestCeilingIsolationLayer();

            if (!$ceilingIsolation || $ceilingIsolation->getSize() <= 5) {
                $m = new Material();
                $m->setLambda($this->presentStyrofoamLambda);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(10);

                $house->setLowestCeilingIsolationLayer($l);
                
                $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie podłogi 10cm styropianu'
                );
            }
        }
    }

    protected function tryBetterGroundFloorIsolation($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();

        if ($this->building->isGroundFloorHeated()) {
            $groundFloorIsolation = $house->getGroundFloorIsolationLayer();
            $materialName = $groundFloorIsolation && $groundFloorIsolation->getMaterial() ? $groundFloorIsolation->getMaterial()->getName() : '';

            if (!$groundFloorIsolation || $groundFloorIsolation->getSize() <= 10 || (!stristr($materialName, 'styropian') && !stristr($materialName, 'wełna'))) {
                $m = new Material();
                $m->setLambda($this->presentStyrofoamLambda);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(20);

                $house->setGroundFloorIsolationLayer($l);
                
                $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie podłogi parteru 20cm styropianu'
                );
            }
        }
    }

    protected function tryBetterGroundFloorCeilingIsolation($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();

        if (!$this->building->isGroundFloorHeated()) {
            $ceilingIsolation = $house->getLowestCeilingIsolationLayer();

            if (!$ceilingIsolation || $ceilingIsolation->getSize() <= 5) {
                $m = new Material();
                $m->setLambda($this->presentStyrofoamLambda);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(10);

                $house->setLowestCeilingIsolationLayer($l);
                
                $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie stropu nad parterem 10cm styropianu'
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
                'title' => 'wymiana drzwi'
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
                'title' => 'wymiana wszystkich okien'
            );
        }
    }

    protected function tryBetterWallIsolation($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);

        $originalWall = $originalCalculation->getHouse()->getWalls()->first();

        if (!$originalWall->getExtraIsolationLayer() || $originalWall->getExtraIsolationLayer()->getSize() < 10) {
            $isolationSize = 15;

            $wall = $customCalculation->getHouse()->getWalls()->first();

            $m = new Material();
            $m->setLambda($this->presentStyrofoamLambda);

            $l = new Layer();
            $l->setMaterial($m);
            $l->setSize($isolationSize);

            $wall->setExtraIsolationLayer($l);
            
            $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => sprintf('ocieplenie ścian zewn. %scm styropianu, lambda %s', $isolationSize, $this->presentStyrofoamLambda)
            );
        }
    }

    protected function tryBetterRoofIsolation($originalCalculation, $actualEnergyLoss, array &$variants)
    {
        $customCalculation = clone unserialize(serialize($originalCalculation));
        $this->instance->setCustomCalculation($customCalculation);
        $house = $customCalculation->getHouse();
        $roofType = $house->getRoofType();

        $flatRoof = $roofType == 'flat' || $roofType == false;

        $roofIsolation = $flatRoof ? $house->getHighestCeilingIsolationLayer() : $house->getRoofIsolationLayer();
        $materialName = $roofIsolation && $roofIsolation->getMaterial() ? $roofIsolation->getMaterial()->getName() : '';

        if (!$roofIsolation || $roofIsolation->getSize() < 20 || (!stristr($materialName, 'styropian') && !stristr($materialName, 'wełna'))) {
            $isolationSize = $flatRoof ? 35 : 20;
            
            $m = new Material();
            $m->setLambda($this->presentStyrofoamLambda);

            $l = new Layer();
            $l->setMaterial($m);
            $l->setSize($isolationSize);

            if ($flatRoof || !$this->building->isAtticHeated()) {
                $house->setHighestCeilingIsolationLayer($l);
            } else {
                $house->setRoofIsolationLayer($l);
            }
            
            $newEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => sprintf('ocieplenie dachu %scm styropianu', $isolationSize)
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
            'title' => 'wentylacja mechaniczna z odzyskiem ciepła'
        );
    }
}
