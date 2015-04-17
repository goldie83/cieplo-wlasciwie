<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Calculator\BuildingInterface;
use Kraken\WarmBundle\Service\InstanceService;
use Doctrine\ORM\EntityManager;

class ComparisonService
{
    protected $instance;
    protected $em;
    protected $fuel;

    public function __construct(InstanceService $instance, EntityManager $em, FuelService $fuel)
    {
        $this->instance = $instance;
        $this->em = $em;
        $this->fuel = $fuel;
    }

    public function compareFuelConsumption()
    {
        $calc = $this->instance->get();

        $result = $this->em
            ->createQueryBuilder()
            ->select('COUNT(c.id) as cnt, AVG(c.fuel_consumption) as average')
            ->from('KrakenWarmBundle:Calculation', 'c')
            ->where('c.city = ?0')
            ->andWhere('c.heated_area BETWEEN ?1 AND ?2')
            ->andWhere('c.heating_power BETWEEN ?3 AND ?4')
            ->andWhere('c.fuel_type = ?5')
            ->andWhere('c.stove_type = ?6')
            ->andWhere('c.fuel_consumption > ?7')
            ->setParameters(array(
                0 => $calc->getCity(),
                1 => $calc->getHeatedArea()*0.8,
                2 => $calc->getHeatedArea()*1.2,
                3 => $calc->getHeatingPower()*0.8,
                4 => $calc->getHeatingPower()*1.2,
                5 => $calc->getFuelType(),
                6 => $calc->getStoveType(),
                7 => 0,
            ))
            ->getQuery()
            ->getSingleResult();

        if (!$result['average'] || $result['cnt'] < 3) {
            return '';
        }

        return sprintf('Podobne budynki w okolicy zużywają średnio <strong>%s</strong>.', $this->fuel->formatFuelAmount($result['average'], $calc));
    }
}
