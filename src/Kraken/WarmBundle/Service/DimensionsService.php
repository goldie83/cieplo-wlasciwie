<?php

namespace Kraken\WarmBundle\Service;

class DimensionsService
{
    private $instance;
    private $floors;

    const STANDARD_WINDOW_AREA = 2.5; // 1,4x1,8m
    const BALCONY_DOOR_AREA = 2; // 2x1m
    const HUGE_GLAZING_AREA = 6.25; // 2,5x3m
    const CEILING_THICKNESS = 0.35;

    public function __construct(InstanceService $instance, FloorsService $floors)
    {
        $this->instance = $instance;
        $this->floors = $floors;
    }

    public function getInstance()
    {
        return $this->instance->get();
    }

    public function getStandardWindowArea()
    {
        return self::STANDARD_WINDOW_AREA;
    }

    public function getWindowsArea()
    {
        $house = $this->getInstance()->getHouse();

        return self::STANDARD_WINDOW_AREA * $house->getNumberWindows()
            + self::BALCONY_DOOR_AREA * $house->getNumberBalconyDoors()
            + self::HUGE_GLAZING_AREA * $house->getNumberHugeGlazings();
    }

    public function getTotalWallArea()
    {
        $houseHeight = $this->getHouseHeight();
        $l = $this->getExternalBuildingLength();
        $w = $this->getExternalBuildingWidth();

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

        if (!$this->getInstance()->isApartment() && $this->getInstance()->getHouse()->getBuildingRoof() == 'steep') {
            $houseHeight -= 0.8 * $this->getInstance()->getHouse()->getFloorHeight();
        }

        return $sum * $houseHeight;
    }

    public function getNetWallArea()
    {
        return $this->getTotalWallArea() - $this->getWallOpeningsArea();
    }

    public function getWallOpeningsArea()
    {
        return $this->getWindowsArea() + $this->getDoorsArea();
    }

    public function getBasementHeight()
    {
        return 0.9 * $this->getInstance()->getHouse()->getFloorHeight();
    }

    public function getNumberOfHeatedFloors()
    {
        return count($this->getInstance()->getHouse()->getBuildingHeatedFloors());
    }

    public function getExternalBuildingLength()
    {
        $house = $this->getInstance()->getHouse();

        if ($house->getArea() > 0) {
            return ceil(sqrt($house->getArea()));
        }

        if ($house->getBuildingShape() == 'irregular') {
            return $house->getBuildingLength() + floor(sqrt($house->getBuildingContourFreeArea()));
        }

        return $house->getBuildingLength();
    }

    public function getExternalBuildingWidth()
    {
        $house = $this->getInstance()->getHouse();

        if ($house->getArea() > 0) {
            return ceil(sqrt($house->getArea()));
        }

        if ($house->getBuildingShape() == 'irregular') {
            return $house->getBuildingWidth() + floor(sqrt($house->getBuildingContourFreeArea()));
        }

        return $house->getBuildingWidth();
    }

    public function getInternalBuildingLength()
    {
        return $this->getExternalBuildingLength() - 2 * ($this->getInstance()->getHouse()->getWallSize() / 100);
    }

    public function getInternalBuildingWidth()
    {
        return $this->getExternalBuildingWidth() - 2 * ($this->getInstance()->getHouse()->getWallSize() / 100);
    }

    public function getFloorArea()
    {
        $house = $this->getInstance()->getHouse();

        if ($house->getArea() > 0) {
            return $house->getArea();
        } else {
            $l = $this->getInternalBuildingLength();
            $w = $this->getInternalBuildingWidth();

            if ($house->getBuildingShape() == 'irregular') {
                return ($l * $w - $house->getBuildingContourFreeArea());
            }

            return $l * $w;
        }
    }

    public function getTotalHouseArea()
    {
        $house = $this->getInstance()->getHouse();
        $area = $this->getFloorArea() * $this->floors->getTotalFloorsNumber();

        if (!$this->getInstance()->isApartment() && $house->getBuildingRoof() == 'steep') {
            $area -= 0.3 * $this->getFloorArea();
        }

        return $area;
    }

    public function getHeatedArea()
    {
        return $this->getHeatedHouseArea();
    }

    public function getHeatedHouseArea()
    {
        $house = $this->getInstance()->getHouse();
        $area = $this->getFloorArea() * $this->floors->getHeatedFloorsNumber();

        if (!$this->getInstance()->isApartment() && $house->getBuildingRoof() == 'steep' && $this->floors->isAtticHeated()) {
            $area -= 0.3 * $this->getFloorArea();
        }

        if ($house->hasGarage() && $this->floors->isGroundFloorHeated()) {
            $area -= 20;
        }

        return $area;
    }

    public function getHouseHeight()
    {
        $numberFloors = $this->getNumberOfHeatedFloors();

        if (!$this->getInstance()->isApartment() && $this->floors->isBasementHeated()) {
            --$numberFloors;
        }

        return $numberFloors *  $this->getInstance()->getHouse()->getFloorHeight() + $numberFloors * self::CEILING_THICKNESS;
    }

    public function getDoorsArea()
    {
        return $this->getStandardDoorArea() * $this->getInstance()->getHouse()->getNumberDoors();
    }

    public function getStandardDoorArea()
    {
        $doorHeight = $this->getInstance()->getHouse()->getFloorHeight() * 0.8;
        $doorWidth = 1;

        return $doorHeight * $doorWidth;
    }

    public function getHouseCubature()
    {
        $cubature = 0;
        // we're interested in heated room only
        $numberFloors = $this->getNumberOfHeatedFloors();

        for ($i = 0; $i < $numberFloors; ++$i) {
            $floorCubature = $this->getFloorArea() *  $this->getInstance()->getHouse()->getFloorHeight();

            $cubature += $i == 0 && !$this->getInstance()->isApartment() && $this->getInstance()->getHouse()->getBuildingRoof() == 'steep'
                ? $floorCubature * 0.6
                : $floorCubature;
        }

        return round($cubature, 2);
    }

    public function getRoofArea()
    {
        $l = $this->getExternalBuildingLength();
        $w = $this->getExternalBuildingWidth();

        if ($this->getInstance()->getHouse()->getBuildingRoof() == 'steep') {
            $w = sqrt(pow($this->getExternalBuildingWidth() / 2, 2) + pow(2.6, 2));

            return round(2 * $w * $l, 2);
        }

        return round($l * $w, 2);
    }

    public function getNumberOfWalls()
    {
        if ($this->getInstance()->isApartment()) {
            return $this->getInstance()->getHouse()->getApartment()->getNumberExternalWalls();
        } elseif ($this->getInstance()->getBuildingType() == 'row_house') {
            return $this->getInstance()->getHouse()->isRowHouseOnCorner() ? 3 : 2;
        } elseif ($this->getInstance()->getBuildingType() == 'double_house') {
            return 3;
        }

        return 4;
    }
}
