<?php

namespace Kraken\WarmBundle\Calculator;

use Kraken\WarmBundle\Service\DimensionsService;
use Kraken\WarmBundle\Service\FuelService;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\HotWaterService;

class EnergyCalculator
{
    protected $instance;
    protected $heating_season;
    protected $fuel_service;
    protected $building;
    protected $climate;
    protected $dimensions;
    protected $hotWater;

    public function __construct(InstanceService $instance, HeatingSeason $heatingSeason, FuelService $fuelService, BuildingInterface $building, ClimateZoneService $climate, DimensionsService $dimensions, HotWaterService $hotWater)
    {
        $this->instance = $instance;
        $this->heating_season = $heatingSeason;
        $this->fuel_service = $fuelService;
        $this->building = $building;
        $this->climate = $climate;
        $this->dimensions = $dimensions;
        $this->hotWater = $hotWater;
    }

    /*
     * Amount of energy in kWh needed during the whole heating season (depends on location)
     */
    public function getYearlyEnergyConsumption()
    {
        $heatingSeasonTemperatures = $this->heating_season->getDailyTemperatures();
        $energy = 0;

        foreach ($heatingSeasonTemperatures as $t) {
            $energy += $this->getHeatingPower($t->getValue()) * 24; // 24h
        }

        return $energy / 1000;
    }

    /*
     * Amount of energy in kWh consumed during previous heating season (depends on location)
     */
    public function getLastYearEnergyConsumption()
    {
        $lastYearTemperatures = $this->heating_season->getLastYearDailyTemperatures();
        $energy = 0;

        foreach ($lastYearTemperatures as $t) {
            $energy += $this->getHeatingPower($t->getValue()) * 24; // 24h
        }

        return $energy / 1000;
    }

    public function getNecessaryStovePower($fuel = 'coal')
    {
        $power = 1.1 * ($this->getMaxHeatingPower() / 1000);

        if ($fuel == 'sand_coal') {
            $power *= 2;
        }

        if ($this->hotWater->isIncluded()) {
            $power += $this->hotWater->getPower();
        }

        return $power;
    }

    public function getSuggestedAutomaticStovePower()
    {
        $powerNeeded = $this->getMaxHeatingPower() / 1000;

        if ($this->hotWater->isIncluded()) {
            $powerNeeded += $this->hotWater->getPower();
        }

        $variants = [
            8 => '7-10',
            12 => '10-12',
            14 => '12-14',
            16 => '14-15',
            19 => '17',
            26 => '24',
            38 => '35',
            42 => '40',
        ];

        foreach ($variants as $threshold => $power) {
            if ($powerNeeded <= $threshold) {
                return $power;
            }
        }

        return 1.1 * $powerNeeded;
    }

    public function getYearlyEnergyConsumptionFactor()
    {
        return $this->getYearlyEnergyConsumption() / $this->dimensions->getHeatedArea();
    }

    public function getYearlyStoveEfficiency()
    {
        $paidEnergy = $this->getEnergyOfSpentFuel();

        if ($paidEnergy == 0) {
            throw new \RuntimeException('Fuel consumption info not provided');
        }

        $efficiency = $this->getLastYearEnergyConsumption() / $paidEnergy;

        return round($efficiency, 1);
    }

    /*
     * Get energy in kWh contained in consumed amount of fuel
     */
    public function getEnergyOfSpentFuel()
    {
        $totalEnergy = 0;

        foreach ($this->instance->get()->getFuelConsumptions() as $fc) {
            if ($fc->getFuel()) {
                $totalEnergy += $this->fuel_service->getFuelEnergy($fc->getFuel(), $fc->getConsumption());
            }
        }

        // 10% as equivalent of kindling wood etc.
        return 1.1 * $totalEnergy;
    }

    /*
     * Heat demand factor derived from maximum heating power
     */
    public function getHeatDemandFactor()
    {
        return $this->getMaxHeatingPower() / $this->dimensions->getHeatedArea();
    }

    /*
     * Heating power in Watts needed for given outdoor temperature
     */
    public function getHeatingPower($outdoorTemp)
    {
        return ($this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated()) * $this->getTemperatureDifference($outdoorTemp);
    }

    /*
     * Returns maximum heating power needed for lowest outdoor temperatures
     */
    public function getMaxHeatingPower()
    {
        $outdoorTemp = $this->climate->getDesignOutdoorTemperature();

        return ($this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated()) * $this->getTemperatureDifference($outdoorTemp);
    }

    public function getMaxHeatingPowerPerArea()
    {
        return $this->getMaxHeatingPower() / $this->dimensions->getHeatedArea();
    }

    /*
     * Returns average heating power needed during heating season
     */
    public function getAvgHeatingPower()
    {
        $avgTemp = $this->heating_season->getAverageTemperature();

        return ($this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated()) * $this->getTemperatureDifference($avgTemp);
    }

    public function getTemperatureDifference($outdoorTemp)
    {
        return $this->instance->get()->getIndoorTemperature() - $outdoorTemp;
    }

    public function isStoveOversized()
    {
        if (!$this->instance->get()->isUsingSolidFuel()) {
            return false;
        }

        $actualStovePower = $this->instance->get()->getStovePower();

        if (!$actualStovePower) {
            return false;
        }

        if ($this->instance->get()->getFuelType() == 'sand_coal') {
            return $actualStovePower > 1.5 * $this->getNecessaryStovePower('sand_coal');
        }

        $factor = $this->getNecessaryStovePower() > 10 ? 1.5 : 2;

        return $actualStovePower > $factor * $this->getNecessaryStovePower();
    }

    public function getDailyFuelConsumption($fuel, $day)
    {
        $energy = [
            'coal' => 7.8 * 0.6,
            'wood' => 5.5 * 0.6,
            'natural_gas' => 10.5 * 0.9,
        ];

        $hourlyDemand = $day == 'max' ? $this->getMaxHeatingPower() : $this->getAvgHeatingPower();
        $dailyDemand = ($hourlyDemand * 24) / 1000;

        return ceil($dailyDemand / $energy[$fuel]);
    }

    public function getYearlyFuelConsumption($fuel)
    {
        $energy = [
            'coal' => 7.8 * 0.6,
            'wood' => 5.5 * 0.6,
            'natural_gas' => 10.5 * 0.9,
        ];

        $amount = ceil($this->getYearlyEnergyConsumption() / $energy[$fuel]);

        if ($fuel == 'coal') {
            $amount /= 1000;
        } elseif ($fuel == 'wood') {
            $amount = ceil($amount / 500);
        }

        return $amount;
    }
}
