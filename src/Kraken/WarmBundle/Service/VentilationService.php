<?php

namespace Kraken\WarmBundle\Service;

class VentilationService
{
    protected $instance;
    protected $house_service;
    protected $dimensions;

    public function __construct(InstanceService $instance, DimensionsService $dimensions)
    {
        $this->instance = $instance;
        $this->dimensions = $dimensions;
    }

    public function getAirStream(Building $building)
    {
        $airCapacity = $this->dimensions->getHouseCubature();
        $exchangeMultiplicity = $this->getAirExchangeMultiplicity($building);
        $neighbourhoodClosenessFactor = 0.03;
        $buildingHeightFactor = $this->dimensions->getHouseHeight() > 10 ? 1.2 : 1;

        $infiltration = $airCapacity * $exchangeMultiplicity * $neighbourhoodClosenessFactor * $buildingHeightFactor;

        return max($infiltration, $this->getMinimalAirStream($building));
    }

    public function getMinimalAirStream(Building $building)
    {
        $airCapacity = $this->dimensions->getHouseCubature();

        return 0.5 * $airCapacity;
    }

    public function getAirExchangeMultiplicity(Building $building)
    {
        $house = $building->getHouse();
        $type = $house->getVentilationType();
        $windowsType = $house->getWindowsType();

        $year = $this->instance->get()->getConstructionYear();

        $exchangeThroughLeaks = 0;

        if ($year < 1950) {
            $exchangeThroughLeaks = 0.5;
        } elseif ($year < 1990) {
            $exchangeThroughLeaks = 0.25;
        }

        if ($type == 'natural') {
            if ($windowsType == 'old_single_glass') {
                return 4 + $exchangeThroughLeaks;
            } elseif ($windowsType == 'old_improved') {
                return 2.5 + $exchangeThroughLeaks;
            } elseif ($windowsType == 'old_double_glass') {
                return 2 + $exchangeThroughLeaks;
            } elseif ($windowsType == 'semi_new_double_glass') {
                return 1.5 + $exchangeThroughLeaks;
            }

            return 1.25;
        }

        return 0.8;
    }
}
