<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Calculator\BuildingInterface;
use Kraken\WarmBundle\Entity\Wall;

class Apartment extends Building implements BuildingInterface
{
    public function getEnergyLossBreakdown()
    {
        $w = $this->getWallsEnergyLossFactor();
        $v = $this->getVentilationEnergyLossFactor();
        $g = $this->getFloorEnergyLossFactor();
        $r = $this->getCeilingEnergyLossFactor();
        $win = $this->getWindowsEnergyLossFactor();
        $d = $this->getDoorsEnergyLossFactor();
        $u = $this->getWallsEnergyLossToUnheated()
            + $this->getCeilingEnergyLossToUnheated()
            + $this->getFloorEnergyLossToUnheated();

        $sum = $w + $v + $g + $r + $win + $d + $u;
        $round = function ($number) {
            return $number * 100;
        };

        $breakdown = array(
          'Wentylacja' => $round($v / $sum),
          'Ściany zewnętrzne' => $round($w / $sum),
          'Nieogrzewane <br/><b>pomieszczenia</b>' => $round($u / $sum),
          'Sufit' => $round($r / $sum),
          'Podłoga' => $round($g / $sum),
          'Okna' => $round($win / $sum),
          'Drzwi' => $round($d / $sum),
        );

        foreach ($breakdown as $label => $value) {
            if ((int) $value == 0) {
                unset($breakdown[$label]);
            }
        }

        asort($breakdown);

        return $breakdown;
    }

    public function getEnergyLossToOutside()
    {
        return $this->getWallsEnergyLossFactor()
            + $this->getCeilingEnergyLossFactor()
            + $this->getFloorEnergyLossFactor();
    }

    public function getEnergyLossToUnheated($addWallsIsolation = false)
    {
        return 0.5 * $this->getFloorEnergyLossToUnheated()
            + $this->getCeilingEnergyLossToUnheated()
            + $this->getWallsEnergyLossToUnheated($addWallsIsolation);
    }

    public function getFloorEnergyLossToUnheated()
    {
        $house = $this->getInstance()->getHouse();
        $whatsUnder = $house->getApartment()->getWhatsUnder();

        if ($whatsUnder != 'unheated_room') {
            return 0;
        }

        $lowestCeilingIsolation = $house->getLowestCeilingIsolationLayer();

        $ceilingIsolationResistance = $lowestCeilingIsolation
            ? ($lowestCeilingIsolation->getSize() / 100) / $lowestCeilingIsolation->getMaterial()->getLambda()
            : 0;

        return $house->getExternalBuildingLength() * $house->getExternalBuildingWidth() * 1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance);
    }

    public function getCeilingEnergyLossToUnheated()
    {
        $house = $this->getInstance()->getHouse();
        $whatsOver = $house->getApartment()->getWhatsOver();

        if ($whatsOver != 'unheated_room') {
            return 0;
        }

        $highestCeilingIsolation = $house->getHighestCeilingIsolationLayer();

        $ceilingIsolationResistance = $highestCeilingIsolation
            ? ($highestCeilingIsolation->getSize() / 100) / $highestCeilingIsolation->getMaterial()->getLambda()
            : 0;

        return $house->getExternalBuildingLength() * $house->getExternalBuildingWidth() * 1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance);
    }

    public function getNumberOfWalls()
    {
        return $this->getInstance()->getHouse()->getApartment()->getNumberExternalWalls();
    }

    public function getWallsEnergyLossToUnheated($addIsolation = false)
    {
        $internalWall = $this->wall_factory->getInternalWall($this->getInstance(), $addIsolation);

        return $this->wall->getThermalConductance() * $this->getInternalWallArea();
    }

    public function getWallsEnergyLossFactor()
    {
        return $this->wall->getThermalConductance() * $this->getRealWallArea()
            + $this->getDoorsEnergyLossFactor()
            + $this->getWindowsEnergyLossFactor();
    }

    public function getInternalWallArea()
    {
        $houseHeight = $this->getHouseHeight();

        $l = $this->getInstance()->getHouse()->getExternalBuildingLength();
        $w = $this->getInstance()->getHouse()->getExternalBuildingWidth();

        $walls = $this->getInstance()->getHouse()->getApartment()->getNumberUnheatedWalls();
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

    public function getDoorsEnergyLossFactor(Wall $wall = null)
    {
        $wall = $this->getInstance()->getHouse()->getWalls()->first();
        $house = $wall->getHouse();

        return $this->doors_u_factor[$house->getDoorsType()] * $this->getDoorsArea($house);
    }

    public function getCeilingEnergyLossFactor()
    {
        $house = $this->getInstance()->getHouse();
        $whatsOver = $house->getApartment()->getWhatsOver();

        if ($whatsOver == 'outdoor') {
            $highestCeilingIsolation = $house->getHighestCeilingIsolationLayer();

            $ceilingIsolationResistance = $highestCeilingIsolation
                ? ($highestCeilingIsolation->getSize() / 100) / $highestCeilingIsolation->getMaterial()->getLambda()
                : 0;

            return $house->getExternalBuildingLength() * $house->getExternalBuildingWidth() * 1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance);
        }

        return 0;
    }

    public function getFloorEnergyLossFactor()
    {
        $house = $this->getInstance()->getHouse();
        $l = $house->getExternalBuildingLength();
        $w = $house->getExternalBuildingWidth();
        $floorArea = $l * $w;

        $what = $house->getApartment()->getWhatsUnder();

        if ($what == 'outdoor') {
            $lowestCeilingIsolation = $house->getLowestCeilingIsolationLayer();

            $ceilingIsolationResistance = $lowestCeilingIsolation
                ? ($lowestCeilingIsolation->getSize() / 100) / $lowestCeilingIsolation->getMaterial()->getLambda()
                : 0;

            return $floorArea * 1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance);
        } elseif ($what == 'ground') {
            $isolation = $house->getLowestCeilingIsolationLayer();
            $isolationResistance = $isolation ? ($isolation->getSize() / 100) / $isolation->getMaterial()->getLambda() : 0;

            $groundLambda = $this->getGroundLambda();
            $floorLambda = $isolationResistance > 0
                ? 1 / $isolationResistance
                : 1;
            $wallSize = $house->getWallSize()/100;

            $proportion = ($l * $w) / (0.5 * ($l + $w));
            $equivalentSize = $wallSize + $groundLambda / $floorLambda;

            if ($equivalentSize < $proportion) {
                $equivalentLambda = (2 * $groundLambda / (3.14 * $proportion + $equivalentSize)) * log(3.14 * $proportion / $equivalentSize + 1);
            } else {
                $equivalentLambda = $groundLambda / (0.457 * $proportion + $equivalentSize);
            }

            return round($l * $w * $equivalentLambda, 2);
        }

        return 0;
    }

    public function getHouseCubature()
    {
        $cubature = 0;
        // we're interested in heated room only
        $numberFloors = $this->getNumberOfHeatedFloors();
        for ($i = 0; $i < $numberFloors; ++$i) {
            $cubature += $this->getInternalBuildingLength() * $this->getInternalBuildingWidth() * $this->getFloorHeight();
        }

        return $cubature;
    }

    public function getNumberOfHeatedFloors()
    {
        $house = $this->getInstance()->getHouse();

        return $house->getNumberFloors();
    }

    public function getFloors()
    {
        $nbFloors = $this->getHouse()->getNumberFloors();
        $nbHeatedFloors = $this->getHouse()->getNumberHeatedFloors();

        $below = $this->getHouse()->getApartment()->getWhatsUnder();
        $above = $this->getHouse()->getApartment()->getWhatsOver();

        $floors = array();
        $i = 0;

        if ($below == 'unheated_room') {
            $floors[] = array(
                'name' => 'basement',
                'label' => false,
                'heated' => false,
            );
        } elseif ($below == 'heated_room') {
            $floors[] = array(
                'name' => 'other',
                'label' => 'other',
                'heated' => true,
            );
        }

        for ($j = 1; $i < $nbFloors; ++$i) {
            $floors[] = array(
                'name' => 'regular_floor_'.$j,
                'label' => ($j++).'. piętro',
                'heated' => true,
            );
        }

        if ($above == 'unheated_room') {
            $floors[] = array(
                'name' => 'attic',
                'label' => false,
                'heated' => false,
            );
        } elseif ($above == 'heated_room') {
            $floors[] = array(
                'name' => 'other',
                'label' => 'other',
                'heated' => true,
            );
        }

        return $floors;
    }
}
