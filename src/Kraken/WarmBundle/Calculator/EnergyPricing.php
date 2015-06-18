<?php

namespace Kraken\WarmBundle\Calculator;

use Doctrine\ORM\EntityManager;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\HeatingVariant;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Calculator\HeatingSeason;

class EnergyPricing
{
    protected $em;
    protected $instance;
    protected $calculator;
    protected $heatingSeason;

    public function __construct(InstanceService $instance, EnergyCalculator $calculator, EntityManager $em, HeatingSeason $heatingSeason)
    {
        $this->instance = $instance;
        $this->calculator = $calculator;
        $this->em = $em;
        $this->heatingSeason = $heatingSeason;
    }

    public function getHeatingVariantsComparison()
    {
        $comparison = array();
        $energyAmount = $this->calculator->getYearlyEnergyConsumption();
        
        $heatingVariants = $this->em
            ->createQueryBuilder()
            ->select('hv')
            ->from('KrakenWarmBundle:HeatingVariant', 'hv')
            ->where('hv.legacy = ?1')
            ->setParameter(1, false)
            ->getQuery()
            ->getResult();

        foreach ($heatingVariants as $variant) {
            $amount = ($energyAmount/$variant->getEfficiency())/($variant->getFuel()->getEnergy()*0.277); // MJ to kWh
            $variantKey = $variant->getType();
            $device = $this->instance->get()->getHeatingDevice();
            $actualHeatingDeviceType = $device ? $device->getType() : '';

            if ($actualHeatingDeviceType != 'manual_stove' && $variant->getHeatingDevice()->getType() == $actualHeatingDeviceType) {
                continue;
            }

            $comparison[$variantKey] = [
                'label' => $variant->getName(),
                'detail' => $variant->getDetail(),
                'amount' => $amount,
                'fuel_type' => $variant->getFuel()->getType(),
                'efficiency' => $variant->getEfficiency(),
                'setup_costs' => $this->collectSetupCosts($variant),
                'maintenance_time' => $variant->getMaintenanceTime(),
            ];
        }

        return $comparison;
    }

    protected function collectSetupCosts(HeatingVariant $variant)
    {
        $costs = [
            'chimney' => ['Komin', 10000],
            'boiler_room' => ['Kotłownia', 12000],
            'automatic_stove' => ['Kocioł podajnikowy', 7000],
            'gas_stove' => ['Kocioł gazowy', 5000],
            'pellet_stove' => ['Kocioł na pellet', 9000],
            'manual_stove' => ['Kocioł zasypowy', 3500],
            'automated_manual_stove' => ['Kocioł zasypowy', 4500],
            'holzgas_stove' => ['Kocioł zgazowujący', 6000],
            'gas_network_link' => ['Przyłącze gazowe', 8000],
            'gas_tank' => ['Zbiornik na gaz', 8000],
            'heat_buffer' => ['Zbiornik buforowy', 5000],
            'heaters_wires' => ['Grzałki i instalacja elektryczna', 2000],
            'ground_heat_pump' => ['Sprzęt i robocizna', 35000],
            'air_heat_pump' => ['Sprzęt i robocizna', 25000],
            'place' => ['Miejsce na sprzęt', 5000],
        ];

        $mapping = [
            'bituminous_coal_manual_stove' => ['chimney', 'boiler_room', 'manual_stove'],
            'brown_coal_manual_stove' => ['chimney', 'boiler_room', 'manual_stove'],
            'wood_manual_stove' => ['chimney', 'boiler_room', 'manual_stove'],
            'bituminous_coal_manual_stove_buffer' => ['chimney', 'boiler_room', 'manual_stove', 'heat_buffer'],
            'wood_manual_stove_buffer' => ['chimney', 'boiler_room', 'manual_stove', 'heat_buffer'],
            'wood_holzgas_stove' => ['chimney', 'boiler_room', 'holzgas_stove', 'heat_buffer'],
            'sand_coal_manual_stove' => ['chimney', 'boiler_room', 'automated_manual_stove'],
            'coke_manual_stove' => ['chimney', 'boiler_room', 'manual_stove'],
            'eco_coal_automatic_stove' => ['chimney', 'boiler_room', 'automatic_stove'],
            'pellet_pellet_stove' => ['chimney', 'boiler_room', 'pellet_stove'],
            'natural_gas_gas_stove' => ['gas_network_link', 'chimney', 'gas_stove'],
            'natural_gas_gas_stove_condensing' => ['gas_network_link', 'gas_stove'],
            'propane_gas_stove_condensing' => ['gas_tank', 'gas_stove'],
            'electricity_heat_buffer' => ['heat_buffer', 'heaters_wires', 'place'],
            'electricity_heat_pump_air' => ['air_heat_pump', 'place'],
            'electricity_heat_pump_ground' => ['ground_heat_pump', 'place'],
        ];

        $selectedMapping = isset($mapping[$variant->getType()]) 
            ? $mapping[$variant->getType()]
            : [];

        $actualDevice = $this->instance->get()->getHeatingDevice();

        if ($actualDevice) {
            $incurredCosts = [];

            // subtract costs already incurred
            if ($actualDevice->getType() == 'manual_stove') {
                $incurredCosts = ['chimney', 'boiler_room', 'manual_stove'];
            }

            if ($actualDevice->getType() == 'manual_stove_buffer') {
                $incurredCosts = ['chimney', 'boiler_room', 'manual_stove', 'heat_buffer'];
            }

            if ($actualDevice->getType() == 'heat_pump_air') {
                $incurredCosts = ['air_heat_pump'];
            }

            if ($actualDevice->getType() == 'heat_pump_ground') {
                $incurredCosts = ['ground_heat_pump'];
            }

            if ($actualDevice->getType() == 'pellet_stove') {
                $incurredCosts = ['chimney', 'boiler_room', 'pellet_stove'];
            }

            if ($actualDevice->getType() == 'holzgas_stove') {
                $incurredCosts = ['chimney', 'boiler_room', 'holzgas_stove', 'heat_buffer'];
            }

            if ($actualDevice->getType() == 'automatic_stove') {
                $incurredCosts = ['chimney', 'boiler_room', 'automatic_stove'];
            }

            if (stristr($actualDevice->getType(), 'gas')) {
                $incurredCosts = ['gas_stove', 'gas_network_link'];
            }

            if ($actualDevice->getType() == 'tile_stove') {
                $incurredCosts = ['chimney'];
            }

            $selectedMapping = array_diff($selectedMapping, $incurredCosts);
        }

        $selectedCosts = array_values(array_intersect_key($costs, array_flip($selectedMapping)));

        return $selectedCosts;
    }

    public function getMaintenanceTime(Calculation $calc)
    {
        $device = $calc->getHeatingDevice();

        if (!$device) {
            return 0;
        }

        $hv = $this->em
            ->createQueryBuilder()
            ->select('hv')
            ->from('KrakenWarmBundle:HeatingVariant', 'hv')
            ->where('hv.heatingDevice = (?1)')
            ->setParameters([
                1 => $device
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        return $hv ? $hv->getMaintenanceTime() : 0;
    }

    public function getDefaultWorkHourPrice()
    {
        return 8;
    }

    public function getFuels()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('f')
            ->from('KrakenWarmBundle:Fuel', 'f')
            ->groupBy('f.name')
            ->orderBy('f.id')
            ->getQuery()
            ->getResult();
    }
}
