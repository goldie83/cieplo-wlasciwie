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
    protected $wall_factory;

    protected $lossToOutside;
    protected $lossToUnheated;

    const CEILING_THICKNESS = 0.35;
    const HEATING_SEASON_DAYS = 200;

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

    public function __construct(InstanceService $instance, VentilationService $ventilation, WallService $wall, WallFactory $wall_factory)
    {
        $this->instance = $instance;
        $this->ventilation = $ventilation;
        $this->wall = $wall;
        $this->wall_factory = $wall_factory;
    }

    public function getInstance()
    {
        return $this->instance->get();
    }

    public function getEnergyLossBreakdown()
    {
        $house = $this->getHouse();

        $w = $this->getWallsEnergyLossFactor();
        $v = $this->getVentilationEnergyLossFactor();
        $g = $this->getGroundEnergyLossFactor() + $this->getFloorEnergyLossToUnheated();
        $r = $this->getRoofEnergyLossFactor() + $this->getRoofEnergyLossToUnheated();
        $win = $this->getWindowsEnergyLossFactor();
        $d = $this->getDoorsEnergyLossFactor();

        $sum = $w + $v + $g + $r + $win + $d;
        $round = function ($number) {
            return max(1, $number * 100);
        };

        if ($house->getHasBasement() && !$this->isBasementHeated() && $this->isGroundFloorHeated()) {
            $groundLabel = 'Podłoga nad piwnicą';
        } elseif (!$this->isGroundFloorHeated()) {
            $groundLabel = 'Strop nad parterem';
        } else {
            $groundLabel = 'Podłoga na gruncie';
        }

        $roofLabel = 'Dach';
        if ($house->getRoofType() != 'flat' && !$this->isAtticHeated()) {
            $roofLabel = 'Strop poddasza';
        }

        $breakdown = array(
          'Wentylacja' => $round($v / $sum),
          'Ściany zewnętrzne' => $round($w / $sum),
          $roofLabel => $round($r / $sum),
          $groundLabel => $round($g / $sum),
          'Okna' => $round($win / $sum),
          'Drzwi' => $round($d / $sum),
        );

        asort($breakdown);

        return $breakdown;
    }

    public function getNumberOfWalls()
    {
        return 4;
    }

    public function getHouse()
    {
        return $this->getInstance()->getHouse();
    }

    public function isBasementHeated()
    {
        if (!$this->getHouse()->getHasBasement() || $this->getHouse()->getWhatsUnheated() == 'basement') {
            return false;
        }

        $floors = $this->getHouse()->getNumberFloors();
        $heatedFloors = $this->getHouse()->getNumberHeatedFloors();

        return ($floors > 1 && $floors == $heatedFloors) || ($floors - $heatedFloors == 1 && $this->getHouse()->getWhatsUnheated() != 'basement');
    }

    public function isGroundFloorHeated()
    {
        return $this->getHouse()->getNumberFloors() == $this->getHouse()->getNumberHeatedFloors() || $this->getHouse()->getWhatsUnheated() != 'ground_floor';
    }

    public function isAtticHeated()
    {
        if ($this->getHouse()->getRoofType() == 'flat') {
            return false;
        }

        $floors = $this->getHouse()->getNumberFloors();
        $heatedFloors = $this->getHouse()->getNumberHeatedFloors();

        return $floors > 1 && $floors == $heatedFloors ||
            ($floors - $heatedFloors == 1
                && $this->getHouse()->getWhatsUnheated() != 'attic'
                && $this->getHouse()->getWhatsUnheated() != ''
                && !$this->isBasementHeated());
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

    public function getHeatedArea()
    {
        return $this->getHeatedHouseArea();
    }

    public function getWallsEnergyLossFactor()
    {
        $wall = $this->getInstance()->getHouse()->getWalls()->first();

        return $this->getExternalWallEnergyLossFactor($wall)
            + $this->getDoorsEnergyLossFactor($wall)
            + $this->getWindowsEnergyLossFactor($wall);
    }

    public function getExternalWallEnergyLossFactor(Wall $wall)
    {
        return $this->wall->getThermalConductance($wall) * $this->getRealWallArea($wall);
    }

    public function getExternalWallConductance()
    {
        $wall = $this->getInstance()->getHouse()->getWalls()->first();

        return $this->wall->getThermalConductance($wall);
    }

    public function getDoorsConductance()
    {
        $wall = $this->getInstance()->getHouse()->getWalls()->first();
        $house = $wall->getHouse();

        return isset($this->doors_u_factor[$house->getDoorsType()])
            ? $this->doors_u_factor[$house->getDoorsType()]
            : $this->doors_u_factor['other'];
    }

    public function getDoorsEnergyLossFactor(Wall $wall = null)
    {
        $wall = $this->getInstance()->getHouse()->getWalls()->first();
        $house = $wall->getHouse();

        return $this->getDoorsConductance() * $this->getDoorsArea($house);
    }

    /*
     * Energy loss factor for whole windows area in W/K
     */
    public function getWindowsEnergyLossFactor(Wall $wall = null)
    {
        $wall = $this->getInstance()->getHouse()->getWalls()->first();
        $house = $wall->getHouse();

        return $this->windows_u_factor[$house->getWindowsType()] * $this->getWindowsArea();
    }

    public function getWindowsConductance()
    {
        $wall = $this->getInstance()->getHouse()->getWalls()->first();
        $house = $wall->getHouse();

        return $this->windows_u_factor[$house->getWindowsType()];
    }

    public function getRoofConductance()
    {
        $house = $this->getInstance()->getHouse();
        $roofType = $house->getRoofType();

        if ($roofType == 'flat' || $roofType == false) {
            $isolation = $house
                ->getHighestCeilingIsolationLayer();
            $roofIsolationResistance = $isolation
                ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda()
                : 0;

            return 1 / ($this->getInternalCeilingResistance() + $roofIsolationResistance);
        }

        if ($this->isAtticHeated()) {
            // assume construction material for non-flat roof
            $woodenCoverLambda = 0.18;
            $woodenCoverSize = 0.1;
            $constructionResistance = $woodenCoverSize / $woodenCoverLambda;

            $isolation = $house->getRoofIsolationLayer();

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
        return $this->getRoofArea() * $this->getRoofConductance();
    }

    public function getHighestCeilingConductance()
    {
        $house = $this->getInstance()->getHouse();

        $isolation = $house
            ->getHighestCeilingIsolationLayer();
        $isolationResistance = $isolation
            ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda()
            : 0;

        return 1 / ($this->getInternalCeilingResistance() + $isolationResistance);
    }

    public function getRoofEnergyLossToUnheated()
    {
        $house = $this->getInstance()->getHouse();

        if ($house->getRoofType() != 'flat' && !$this->isAtticHeated()) {
            return $this->getRoofArea() * $this->getHighestCeilingConductance();
        }

        return 0;
    }

    public function getGroundEnergyLossFactor()
    {
        $house = $this->getInstance()->getHouse();

        if ($this->isGroundFloorHeated()) {
            if ($house->getHasBasement()) {
                if ($this->isBasementHeated()) {
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
        $house = $this->getInstance()->getHouse();
        $l = $house->getBuildingLength();
        $w = $house->getBuildingWidth();

        $isolation = $house->getGroundFloorIsolationLayer();
        $isolationResistance = $isolation ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda() : 0;

        $groundLambda = $this->getGroundLambda();
        $floorLambda = $isolationResistance > 0
            ? 1 / $isolationResistance
            : 1;
        $wallSize = $this->wall->getSize($house->getWalls()->first());

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
        $house = $this->getInstance()->getHouse();
        $l = $house->getBuildingLength();
        $w = $house->getBuildingWidth();

        return round($l * $w * $this->getGroundFloorConductance(), 2);
    }

    public function getUndergroundConductance()
    {
        $house = $this->getInstance()->getHouse();

        if (!($this->isGroundFloorHeated() && $house->getHasBasement() && $this->isBasementHeated())) {
            return 0;
        }

        $l = $house->getBuildingLength();
        $w = $house->getBuildingWidth();

        $isolation = $house->getBasementFloorIsolationLayer();
        $isolationResistance = $isolation
            ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda()
            : 0;

        $basementHeight = $this->getBasementHeight();

        $groundLambda = $this->getGroundLambda();
        $floorLambda = $isolationResistance > 0
            ? 1 / $isolationResistance
            : 1;
        $wallSize = $this->wall->getSize($house->getWalls()->first());
        $floorArea = $l * $w;
        $floorPerimeter = 2 * ($l + $w);

        $proportion = $floorArea / (0.5 * $floorPerimeter);
        $equivalentSize = $wallSize + $groundLambda / $floorLambda;

        if ($equivalentSize + 0.5 * $basementHeight < $proportion) {
            $equivalentFloorLambda = (2 * $groundLambda / (3.14 * $proportion + $equivalentSize + 0.5 * $basementHeight)) * log(3.14 * $proportion / ($equivalentSize + 0.5 * $basementHeight) + 1);
        } else {
            $equivalentFloorLambda = $groundLambda / (0.457 * $proportion + $equivalentSize);
        }

        $equivalentWallSize = $groundLambda / $this->wall->getThermalConductance($house->getWalls()->first());
        $basementWallsLambda = ((2 * $groundLambda) / (3.14 * $basementHeight)) * (1 + (0.5 * $equivalentSize) / ($equivalentSize + $basementHeight)) * log($basementHeight / $equivalentWallSize + 1);

        $totalLambda = ($floorArea * $equivalentFloorLambda + $basementHeight * $floorPerimeter * $basementWallsLambda) / ($floorArea + $basementHeight * $floorPerimeter);

        return $totalLambda;
    }

    public function getEnergyLossToUnderground()
    {
        $house = $this->getInstance()->getHouse();

        $l = $house->getBuildingLength();
        $w = $house->getBuildingWidth();

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

    public function getInternalWallConductance()
    {
        $internalWall = $this->wall_factory->getInternalWall($this->instance->get());

        return $this->wall->getThermalConductance($internalWall);
    }

    public function getFloorEnergyLossToUnheated()
    {
        $house = $this->getInstance()->getHouse();

        if ($house->getHasBasement() && !$this->isBasementHeated()) {
            $wallSize = $this->wall->getSize($house->getWalls()->first());

            $l = $house->getBuildingLength() - 2 * $wallSize;
            $w = $house->getBuildingWidth() - 2 * $wallSize;

            $groundFloorIsolation = $house->getGroundFloorIsolationLayer();

            $ceilingIsolationResistance = $groundFloorIsolation
                ? ($groundFloorIsolation->getSize() / 100) / $groundFloorIsolation->getMaterial()->getLambda()
                : 0;

            return round($l * $w * (1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance)), 2);
        } elseif (!$house->getHasBasement() && !$this->isGroundFloorHeated()) {
            $wallSize = $this->wall->getSize($house->getWalls()->first());

            $l = $house->getBuildingLength() - 2 * $wallSize;
            $w = $house->getBuildingWidth() - 2 * $wallSize;

            $ceilingIsolation = $house->getLowestCeilingIsolationLayer();

            $ceilingIsolationResistance = $ceilingIsolation
                ? ($ceilingIsolation->getSize() / 100) / $ceilingIsolation->getMaterial()->getLambda()
                : 0;

            return round($l * $w * (1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance)), 2);
        }

        return 0;
    }

    public function getVentilationEnergyLossFactor()
    {
        $type = $this->getInstance()->getHouse()->getVentilationType();

        $airStream = $this->ventilation->getAirStream($this);

        if ($type == 'natural' || $type == 'mechanical') {
            return 0.34 * $airStream;
        }

        $heatRecoveryEfficiency = 0.6;

        return 0.34 * (1 - $heatRecoveryEfficiency) * $airStream;
    }

    public function getHouseCubature()
    {
        $cubature = 0;
        // we're interested in heated room only
        $numberFloors = $this->getNumberOfHeatedFloors();

        for ($i = 0; $i < $numberFloors; ++$i) {
            $floorCubature = $this->getInternalBuildingLength() * $this->getInternalBuildingWidth() * $this->getFloorHeight();

            $cubature += $i == 0 && $this->getInstance()->getHouse()->getRoofType() != 'flat'
                ? $floorCubature * 0.5
                : $floorCubature;
        }

        return round($cubature, 2);
    }

    public function getRoofArea()
    {
        $roofType = $this->getInstance()->getHouse()->getRoofType();
        $l = $this->getInstance()->getHouse()->getBuildingLength();
        $w = $this->getInstance()->getHouse()->getBuildingWidth();

        if ($roofType == 'oblique') {
            // 30 degrees
            return 2 * ($w / sqrt(3)) * $l;
        }

        if ($roofType == 'steep') {
            // 60 degrees
            return 2 * $w * $l;
        }

        return $this->getInstance()->getHouse()->getBuildingLength() * $this->getInstance()->getHouse()->getBuildingWidth();
    }

    public function getNumberOfHeatedFloors()
    {
        $house = $this->getInstance()->getHouse();
        $nbHeatedFloors = $house->getNumberHeatedFloors();

        // basement walls are added to ground floor, so we skip basement here
        if ($house->getHasBasement() && $house->getWhatsUnheated() != 'basement') {
            --$nbHeatedFloors;
        }

        if ($nbHeatedFloors < 1) {
            $nbHeatedFloors = 1;
        }

        return $nbHeatedFloors;
    }

    public function getInternalBuildingLength()
    {
        $wall = $this->getInstance()->getHouse()->getWalls()->first();
        $l = $this->getInstance()->getHouse()->getBuildingLength();

        return $l - 2 * $this->wall->getSize($wall);
    }

    public function getInternalBuildingWidth()
    {
        $wall = $this->getInstance()->getHouse()->getWalls()->first();
        $w = $this->getInstance()->getHouse()->getBuildingWidth();

        return $w - 2 * $this->wall->getSize($wall);
    }

    public function getTotalHouseArea()
    {
        return $this->getInternalBuildingLength() * $this->getInternalBuildingWidth() * $this->getHouse()->getNumberFloors();
    }

    public function getHeatedHouseArea()
    {
        $nbFloors = $this->getNumberOfHeatedFloors();

        // this should include any heated area, so we add up basement again, if present
        if ($this->getHouse()->getHasBasement() && $this->getHouse()->getWhatsUnheated() != 'basement' && $this->getInstance()->getHouse()->getNumberHeatedFloors() > 1) {
            ++$nbFloors;
        }

        return $this->getInternalBuildingLength() * $this->getInternalBuildingWidth() * $nbFloors;
    }

    /*
     * Total wall area - simply as rectangle area, including doors and windows.
     */
    public function getWallArea(Wall $wall)
    {
        $houseHeight = $this->getHouseHeight();
        $l = $this->getInstance()->getHouse()->getBuildingLength();
        $w = $this->getInstance()->getHouse()->getBuildingWidth();

        $walls = $this->getNumberOfWalls();
        $sum = 0;

        if ($walls > 0) {
            $sum += $l;
            --$walls;
        }

        if ($walls > 0) {
            $sum += $w;
            --$walls;
        }

        if ($walls > 0) {
            $sum += $l;
            --$walls;
        }

        if ($walls > 0) {
            $sum += $w;
            --$walls;
        }

        return $sum * $houseHeight;
    }

    public function getFloorHeight()
    {
        return $this->getInstance()->getHouse()->getFloorHeight();
    }

    public function getHouseHeight()
    {
        $numberFloors = $this->getNumberOfHeatedFloors();

        return $numberFloors * $this->getFloorHeight() + ($numberFloors - 1) * self::CEILING_THICKNESS;
    }

    public function getDoorsArea()
    {
        $numberDoors = $this->getInstance()->getHouse()->getNumberDoors();
        $hasGarage = $this->getInstance()->getHouse()->getHasGarage();

        // garage door is ignored
        $sum = $this->getStandardDoorArea() * $numberDoors;

        if (!$this->isGroundFloorHeated() && $numberDoors > 1) {
            --$numberDoors;
        }

        return $sum;
    }

    public function getStandardDoorArea()
    {
        $doorHeight = $this->getFloorHeight() * 0.8;
        $doorWidth = 1;

        return $doorHeight * $doorWidth;
    }

    public function getStandardWindowArea()
    {
        return 1.4 * 1.8;
    }

    public function getWindowsArea()
    {
        $numberWindows = $this->getInstance()->getHouse()->getNumberWindows();

        return $this->getStandardWindowArea() * $numberWindows;
    }

    /*
     * Doors and windows area in this wall.
     */
    public function getWallOpeningsArea(Wall $wall)
    {
        $house = $wall->getHouse();

        return $this->getWindowsArea($house) + $this->getDoorsArea($house);
    }

    /*
     * True wall area, without doors and windows.
     */
    public function getRealWallArea(Wall $wall)
    {
        return $this->getWallArea($wall) - $this->getWallOpeningsArea($wall);
    }

    public function getBasementHeight()
    {
        return 0.9 * $this->getFloorHeight();
    }

    public function getFloors()
    {
        $nbFloors = $this->getHouse()->getNumberFloors();
        $nbHeatedFloors = $this->getHouse()->getNumberHeatedFloors();

        $unheated = $this->getHouse()->getWhatsUnheated();

        $floors = array();
        $i = 0;

        if ($this->getHouse()->getHasBasement()) {
            $floors[] = array(
                'name' => 'basement',
                'label' => 'Piwnica',
                'heated' => $this->isBasementHeated(),
            );
            ++$i;
        }

        $floors[] = array(
            'name' => 'ground_floor',
            'label' => 'Parter',
            'heated' => $this->isGroundFloorHeated(),
        );
        ++$i;

        for ($j = 1; $i < $nbFloors - 1; ++$i) {
            $floors[] = array(
                'name' => 'regular_floor_'.$j,
                'label' => ($j++).'. piętro',
                'heated' => $unheated == 'floor' ? $i == $nbFloors - 1 : true,
            );
        }

        $floors[] = array(
            'name' => 'attic',
            'label' => 'Poddasze',
            'heated' => true && $unheated != 'attic',
        );

        return $floors;
    }
}
