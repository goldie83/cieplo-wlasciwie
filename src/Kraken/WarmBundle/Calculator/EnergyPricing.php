<?php

namespace Kraken\WarmBundle\Calculator;

use Doctrine\ORM\EntityManager;
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

    public function getEnergySourcesComparison()
    {
        $comparison = array();
        $energyAmount = $this->calculator->getYearlyEnergyConsumption();
        
        $heatingVariants = $this->em
            ->createQueryBuilder()
            ->select('hv')
            ->from('KrakenWarmBundle:HeatingVariant', 'hv')
            ->getQuery()
            ->getResult();

        foreach ($heatingVariants as $variant) {
            $amount = ($energyAmount/$variant->getEfficiency())/($variant->getFuel()->getEnergy()*0.277); // MJ to kWh
            $variantKey = isset($comparison[$variant->getType()]) ? $variant->getType().'_1' : $variant->getType();
            $comparison[$variantKey] = [
                'label' => $variant->getName(),
                'detail' => $variant->getDetail(),
                'amount' => $amount,
                'price' => $variant->getFuel()->getPrice(),
                'trade_amount' => $variant->getFuel()->getTradeAmount(),
                'trade_unit' => $variant->getFuel()->getTradeUnit(),
                'efficiency' => $variant->getEfficiency(),
                'setup_cost' => $variant->getSetupCost(),
                'maintenance_time' => $variant->getMaintenanceTime(),
                'is_legacy' => $variant->isLegacy(),
            ];
        }

        return $comparison;
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
