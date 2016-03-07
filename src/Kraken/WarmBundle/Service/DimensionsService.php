<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Wall;

class DimensionsService
{
    private $instance;

    const STANDARD_WINDOW_AREA = 2.5; // 1,4x1,8m
    const BALCONY_DOOR_AREA = 2; // 2x1m
    const HUGE_GLAZING_AREA = 6.25; // 2,5x3m
    const CEILING_THICKNESS = 0.35;

    public function __construct(InstanceService $instance)
    {
        $this->instance = $instance;
    }

    public function getInstance()
    {
        return $this->instance->get();
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
//TODO nieregularna chaupa

        return $house->getArea() > 0 ? ceil(sqrt($house->getArea())) : $house->getBuildingLength();
    }

    public function getExternalBuildingWidth()
    {
        $house = $this->getInstance()->getHouse();
//TODO nieregularna chaupa

        return $house->getArea() > 0 ? ceil(sqrt($house->getArea())) : $house->getBuildingWidth();
    }

    public function getInternalBuildingLength()
    {
        return $this->getExternalBuildingLength() - 2 * ($this->getInstance()->getHouse()->getWallSize()/100);
    }

    public function getInternalBuildingWidth()
    {
        return $this->getExternalBuildingWidth() - 2 * ($this->getInstance()->getHouse()->getWallSize()/100);
    }

    public function getFloorArea()
    {
        $house = $this->getInstance()->getHouse();

        if ($house->getArea() > 0) {
            return $house->getArea();
        } else {
            $wallSize = $house->getWallSize() / 100;
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
        return $this->getFloorArea() * $this->getInstance()->getHouse()->getBuildingFloors();
    }

    public function getHeatedArea()
    {
        return $this->getHeatedHouseArea();
    }

    public function getHeatedHouseArea()
    {
//TODO minus garaż
        return $this->getFloorArea() * count($this->getInstance()->getHouse()->getBuildingHeatedFloors());
    }

    public function getHouseHeight()
    {
        $numberFloors = $this->getNumberOfHeatedFloors();

        return $numberFloors *  $this->getInstance()->getHouse()->getFloorHeight() + ($numberFloors - 1) * self::CEILING_THICKNESS;
    }

    public function getDoorsArea()
    {
        return $this->getStandardDoorArea() * $this->getInstance()->getHouse()->getNumberDoors();
    }

    public function getStandardDoorArea()
    {
        $doorHeight =  $this->getInstance()->getHouse()->getFloorHeight() * 0.8;
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

            $cubature += $i == 0 && $this->getInstance()->getHouse()->getBuildingRoof() != 'flat'
                ? $floorCubature * 0.6
                : $floorCubature;
        }

        return round($cubature, 2);
    }

    public function getRoofArea()
    {
        $roofType = $this->getInstance()->getHouse()->getBuildingRoof();
        $l = $this->getExternalBuildingLength();
        $w = $this->getExternalBuildingWidth();

        if ($roofType == 'oblique') {
            // 30 degrees
            return 2 * ($w / sqrt(3)) * $l;
        }

        if ($roofType == 'steep') {
            // 60 degrees
            return 2 * $w * $l;
        }

        return $l * $w;
    }

    public function getNumberOfWalls()
    {
        if ($this->getInstance()->getBuildingType() == 'apartment') {
            return $this->getInstance()->getHouse()->getApartment()->getNumberExternalWalls();
        } elseif ($this->getInstance()->getBuildingType() == 'row_house') {
            return 2;
        } elseif ($this->getInstance()->getBuildingType() == 'double_house') {
            return 3;
        }

        return 4;
    }
}
