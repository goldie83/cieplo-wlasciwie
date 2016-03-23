<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Calculator\BuildingInterface;
use Kraken\WarmBundle\Entity\Wall;

class Apartment extends Building implements BuildingInterface
{
    public function getEnergyLossBreakdown()
    {
        $w = $this->getExternalWallEnergyLossFactor() + $this->getWallsEnergyLossToUnheated();
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

//     public function getEnergyLossToOutside()
//     {
//         return $this->lossToOutside = $this->getWallsEnergyLossFactor()
//                 + $this->getCeilingEnergyLossFactor()
//                 + $this->getFloorEnergyLossFactor();
//                 + $this->getVentilationEnergyLossFactor();
//     }

    public function getEnergyLossToOutside()
    {
        return $this->lossToOutside = $this->getWallsEnergyLossFactor()
                + $this->getCeilingEnergyLossFactor()
                + $this->getFloorEnergyLossFactor()
                + $this->getVentilationEnergyLossFactor();
    }

    public function getEnergyLossToUnheated()
    {
        return $this->lossToUnheated = 0.5 * $this->getFloorEnergyLossToUnheated()
                + $this->getCeilingEnergyLossToUnheated()
                + $this->getWallsEnergyLossToUnheated();
    }

    public function getFloorEnergyLossToUnheated()
    {
        $house = $this->getInstance()->getHouse();
        $whatsUnder = $house->getApartment()->getWhatsUnder();

        if ($whatsUnder != 'unheated_room') {
            return 0;
        }

        $lowestCeilingIsolation = $house->getBottomIsolationLayer();

        $ceilingIsolationResistance = $lowestCeilingIsolation
            ? ($lowestCeilingIsolation->getSize() / 100) / $lowestCeilingIsolation->getMaterial()->getLambda()
            : 0;

        return $this->dimensions->getExternalBuildingLength() * $this->dimensions->getExternalBuildingWidth() * 1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance);
    }

    public function getCeilingEnergyLossToUnheated()
    {
        $house = $this->getInstance()->getHouse();
        $whatsOver = $house->getApartment()->getWhatsOver();

        if ($whatsOver != 'unheated_room') {
            return 0;
        }

        $highestCeilingIsolation = $house->getTopIsolationLayer();

        $ceilingIsolationResistance = $highestCeilingIsolation
            ? ($highestCeilingIsolation->getSize() / 100) / $highestCeilingIsolation->getMaterial()->getLambda()
            : 0;

        return $this->dimensions->getExternalBuildingLength() * $this->dimensions->getExternalBuildingWidth() * 1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance);
    }

    public function getWallsEnergyLossToUnheated($addIsolation = false)
    {
        $internalWall = $this->wall_factory->getInternalWall($this->getInstance(), $addIsolation);

        return $this->wall->getThermalConductance() * $this->getInternalWallArea();
    }

    public function getInternalWallArea()
    {
        $houseHeight = $this->dimensions->getHouseHeight();

        $l = $this->dimensions->getExternalBuildingLength();
        $w = $this->dimensions->getExternalBuildingWidth();

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

    public function getCeilingEnergyLossFactor()
    {
        $house = $this->getInstance()->getHouse();
        $whatsOver = $house->getApartment()->getWhatsOver();

        if ($whatsOver == 'outdoor') {
            $highestCeilingIsolation = $house->getTopIsolationLayer();

            $ceilingIsolationResistance = $highestCeilingIsolation
                ? ($highestCeilingIsolation->getSize() / 100) / $highestCeilingIsolation->getMaterial()->getLambda()
                : 0;

            return $this->dimensions->getExternalBuildingLength() * $this->dimensions->getExternalBuildingWidth() * (1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance));
        }

        return 0;
    }

    public function getFloorEnergyLossFactor()
    {
        $house = $this->getInstance()->getHouse();
        $l = $this->dimensions->getExternalBuildingLength();
        $w = $this->dimensions->getExternalBuildingWidth();
        $floorArea = $l * $w;

        $what = $house->getApartment()->getWhatsUnder();

        if ($what == 'outdoor') {
            $lowestCeilingIsolation = $house->getBottomIsolationLayer();

            $ceilingIsolationResistance = $lowestCeilingIsolation
                ? ($lowestCeilingIsolation->getSize() / 100) / $lowestCeilingIsolation->getMaterial()->getLambda()
                : 0;

            return $floorArea * 1 / ($this->getInternalCeilingResistance() + $ceilingIsolationResistance);
        } elseif ($what == 'ground') {
            $isolation = $house->getBottomIsolationLayer();
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
}
