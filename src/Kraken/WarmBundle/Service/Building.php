<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Calculator\BuildingInterface;
use Kraken\WarmBundle\Entity\Wall;

class Building implements BuildingInterface
{
    protected $instance;
    protected $customCalculation;
    protected $house_service;
    protected $ventilation;
    protected $wall;
    protected $dimensions;
    protected $floors;

    protected $lossToOutside;
    protected $lossToUnheated;

    const GROUND_LAMBDA = 1.8;

    protected $windows_u_factor = array(
        '' => 3.0,
        'old_single_glass' => 3.0,
        'old_double_glass' => 2.5,
        'old_improved' => 2.6,
        'semi_new_double_glass' => 1.8,
        'new_double_glass' => 1.1,
        'new_triple_glass' => 0.9,
    );
    protected $doors_u_factor = array(
        '' => 3.0,
        'old_wooden' => 3.0,
        'old_metal' => 2.5,
        'new_wooden' => 1.8,
        'new_wood' => 1.8,
        'new_metal' => 1.9,
        'other' => 2.0,
    );

    public function __construct(InstanceService $instance, VentilationService $ventilation, WallService $wall, DimensionsService $dimensions, FloorsService $floors)
    {
        $this->instance = $instance;
        $this->ventilation = $ventilation;
        $this->wall = $wall;
        $this->dimensions = $dimensions;
        $this->floors = $floors;
    }

    public function getInstance()
    {
        return $this->instance->get();
    }

    public function getEnergyLossBreakdown()
    {
        $w = $this->getExternalWallEnergyLossFactor();
        $v = $this->getVentilationEnergyLossFactor();
        $g = $this->getGroundEnergyLossFactor() + $this->getFloorEnergyLossToUnheated();
        $r = $this->getRoofEnergyLossFactor() + $this->getRoofEnergyLossToUnheated();
        $win = $this->getWindowsEnergyLossFactor();
        $d = $this->getDoorsEnergyLossFactor();

        $sum = $w + $v + $g + $r + $win + $d;
        $round = function ($number) {
            return max(1, $number * 100);
        };

        $breakdown = array(
          'Wentylacja' => $round($v / $sum),
          'Ściany zewnętrzne' => $round($w / $sum),
          $this->floors->getTopLabel() => $round($r / $sum),
          $this->floors->getBottomLabel() => $round($g / $sum),
          'Okna' => $round($win / $sum),
          'Drzwi' => $round($d / $sum),
        );

        asort($breakdown);

        return $breakdown;
    }

    public function getHouse()
    {
        return $this->getInstance()->getHouse();
    }

    public function getEnergyLossToOutside()
    {
        return $this->lossToOutside = $this->getWallsEnergyLossFactor()
                + $this->getRoofEnergyLossFactor()
                + $this->getGroundEnergyLossFactor()
                + $this->getVentilationEnergyLossFactor();
    }

    public function getEnergyLossToUnheated()
    {
        return $this->lossToUnheated = 0.5 * $this->getFloorEnergyLossToUnheated()
                + $this->getRoofEnergyLossToUnheated();
    }

    public function getWallsEnergyLossFactor()
    {
        return $this->getExternalWallEnergyLossFactor()
            + $this->getDoorsEnergyLossFactor()
            + $this->getWindowsEnergyLossFactor();
    }

    public function getExternalWallEnergyLossFactor()
    {
        return $this->wall->getThermalConductance() * $this->dimensions->getTotalWallArea();
    }

    public function getDoorsConductance()
    {
        return isset($this->doors_u_factor[$this->getHouse()->getDoorsType()])
            ? $this->doors_u_factor[$this->getHouse()->getDoorsType()]
            : $this->doors_u_factor['other'];
    }

    public function getDoorsEnergyLossFactor()
    {
        return $this->getDoorsConductance() * $this->dimensions->getDoorsArea();
    }

    /*
     * Energy loss factor for whole windows area in W/K
     */
    public function getWindowsEnergyLossFactor()
    {
        return $this->getWindowsConductance() * $this->dimensions->getWindowsArea();
    }

    public function getWindowsConductance()
    {
        return $this->windows_u_factor[$this->getHouse()->getWindowsType()];
    }

    public function getRoofConductance()
    {
        $house = $this->getHouse();
        $roofType = $house->getBuildingRoof();

        if ($roofType == 'flat' || $roofType == false) {
            $isolation = $house
                ->getTopIsolationLayer();
            $roofIsolationResistance = $isolation
                ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda()
                : 0;

            return 1 / ($this->getInternalCeilingResistance() + $roofIsolationResistance);
        }

        if ($this->floors->isAtticHeated()) {
            // assume construction material for non-flat roof
            $woodenCoverLambda = 0.18;
            $woodenCoverSize = 0.1;
            $constructionResistance = $woodenCoverSize / $woodenCoverLambda;

            $isolation = $house->getTopIsolationLayer();

            $roofIsolationResistance = $isolation
                ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda()
                : 0;

            return 1 / ($constructionResistance + $roofIsolationResistance);
        } else {
            return 0;
        }
    }

    public function getRoofEnergyLossFactor()
    {
        return $this->dimensions->getRoofArea() * $this->getRoofConductance();
    }

    public function getHighestCeilingConductance()
    {
        $house = $this->getHouse();

        $isolation = $house
            ->getTopIsolationLayer();
        $isolationResistance = $isolation
            ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda()
            : 0;

        return 1 / ($this->getInternalCeilingResistance() + $isolationResistance);
    }

    public function getRoofEnergyLossToUnheated()
    {
        $house = $this->getHouse();

        if ($house->getBuildingRoof() != 'flat' && !$this->floors->isAtticHeated()) {
            return $this->dimensions->getRoofArea() * $this->getHighestCeilingConductance();
        }

        return 0;
    }

    public function getGroundEnergyLossFactor()
    {
        if ($this->floors->isGroundFloorHeated()) {
            if ($this->getHouse()->hasBasement()) {
                if ($this->floors->isBasementHeated()) {
                    return $this->getEnergyLossToUnderground();
                }
            } else {
                return $this->getEnergyLossThroughGroundFloor();
            }
        }

        return 0;
    }

    public function getGroundFloorConductance()
    {
        $house = $this->getHouse();
        $l = $this->dimensions->getExternalBuildingLength();
        $w = $this->dimensions->getExternalBuildingWidth();

        $isolation = $house->getBottomIsolationLayer();
        $isolationResistance = $isolation ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda() : 0;

        $groundLambda = self::GROUND_LAMBDA;
        $floorLambda = $isolationResistance > 0
            ? 1 / $isolationResistance
            : 1;
        $wallSize = $house->getWallSize() / 100;

        $proportion = ($l * $w) / (0.5 * ($l + $w));
        $equivalentSize = $wallSize + $groundLambda / $floorLambda;

        if ($equivalentSize < $proportion) {
            $equivalentLambda = (2 * $groundLambda / (3.14 * $proportion + $equivalentSize)) * log(3.14 * $proportion / $equivalentSize + 1);
        } else {
            $equivalentLambda = $groundLambda / (0.457 * $proportion + $equivalentSize);
        }

        return $equivalentLambda;
    }

    public function getEnergyLossThroughGroundFloor()
    {
        $house = $this->getHouse();
        $l = $this->dimensions->getExternalBuildingLength();
        $w = $this->dimensions->getExternalBuildingWidth();

        return round($l * $w * $this->getGroundFloorConductance(), 2);
    }

    public function getUndergroundConductance()
    {
        $house = $this->getHouse();

        if (!($this->floors->isGroundFloorHeated() && $house->hasBasement() && $this->floors->isBasementHeated())) {
            return 0;
        }

        $l = $this->dimensions->getExternalBuildingLength();
        $w = $this->dimensions->getExternalBuildingWidth();

        $isolation = $house->getBottomIsolationLayer();
        $isolationResistance = $isolation
            ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda()
            : 0;

        $basementHeight = $this->dimensions->getBasementHeight();

        $groundLambda = self::GROUND_LAMBDA;
        $floorLambda = $isolationResistance > 0
            ? 1 / $isolationResistance
            : 1;
        $wallSize = $house->getWallSize() / 100;
        $floorArea = $l * $w;
        $floorPerimeter = 2 * ($l + $w);

        $proportion = $floorArea / (0.5 * $floorPerimeter);
        $equivalentSize = $wallSize + $groundLambda / $floorLambda;

        if ($equivalentSize + 0.5 * $basementHeight < $proportion) {
            $equivalentFloorLambda = (2 * $groundLambda / (3.14 * $proportion + $equivalentSize + 0.5 * $basementHeight)) * log(3.14 * $proportion / ($equivalentSize + 0.5 * $basementHeight) + 1);
        } else {
            $equivalentFloorLambda = $groundLambda / (0.457 * $proportion + $equivalentSize);
        }

        $wallConductance = $this->wall->getThermalConductance();
        $equivalentWallSize = $wallConductance > 0 ?  $groundLambda / $wallConductance : 0;
        $basementWallsLambda = ((2 * $groundLambda) / (3.14 * $basementHeight)) * (1 + (0.5 * $equivalentSize) / ($equivalentSize + $basementHeight)) * log($basementHeight / $equivalentWallSize + 1);

        $totalLambda = ($floorArea * $equivalentFloorLambda + $basementHeight * $floorPerimeter * $basementWallsLambda) / ($floorArea + $basementHeight * $floorPerimeter);

        return $totalLambda;
    }

    public function getEnergyLossToUnderground()
    {
        $house = $this->getHouse();

        $l = $this->dimensions->getExternalBuildingLength();
        $w = $this->dimensions->getExternalBuildingWidth();

        return round($l * $w * $this->getUndergroundConductance(), 2);
    }

    public function getGroundLambda()
    {
        return 1.8;
    }

    public function getInternalCeilingResistance()
    {
        $Rsi = 0.17;
        // 0,02m * 0,22W/mK
        $woodenFloor = 0.09;
        // 0,04 * 1,0W/mK
        $concrete = 0.04;
        $dz3 = 0.3;
        $Rse = 0.04;

        return $Rsi + $woodenFloor + $concrete + $dz3 + $Rse;
    }

    public function getFloorEnergyLossToUnheated()
    {
        $house = $this->getHouse();

        if ($house->hasBasement() && !$this->floors->isBasementHeated()) {
            $l = $this->dimensions->getExternalBuildingLength();
            $w = $this->dimensions->getExternalBuildingWidth();

            $groundFloorIsolation = $house->getBottomIsolationLayer();

            $ceilingIsolationResistance = $groundFloorIsolation
                ? ($groundFloorIsolation->getSize() / 100) / $groundFloorIsolation->getMaterial()->getLambda()
                : 0;

            return round($l * $w * (1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance)), 2);
        } elseif (!$house->hasBasement() && !$this->floors->isGroundFloorHeated()) {
            $l = $this->dimensions->getExternalBuildingLength();
            $w = $this->dimensions->getExternalBuildingWidth();

            $ceilingIsolation = $house->getBottomIsolationLayer();

            $ceilingIsolationResistance = $ceilingIsolation
                ? ($ceilingIsolation->getSize() / 100) / $ceilingIsolation->getMaterial()->getLambda()
                : 0;

            return round($l * $w * (1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance)), 2);
        }

        return 0;
    }

    public function getVentilationEnergyLossFactor()
    {
        $type = $this->getHouse()->getVentilationType();

        $airStream = $this->ventilation->getAirStream($this);

        if ($type == 'natural' || $type == 'mechanical') {
            return 0.34 * $airStream;
        }

        $heatRecoveryEfficiency = 0.6;

        return 0.34 * (1 - $heatRecoveryEfficiency) * $airStream;
    }
}
