<?php

namespace Kraken\WarmBundle\Service;

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

    public function getComparables()
    {
        $calc = $this->instance->get();

        $comparables = $this->em
            ->createQueryBuilder()
            ->select('c')
            ->from('KrakenWarmBundle:Calculation', 'c')
            ->where('c.city = ?0')
            ->andWhere('c.heated_area BETWEEN ?1 AND ?2')
            ->andWhere('c.heating_power BETWEEN ?3 AND ?4')
            ->andWhere('c.stove_type = ?5')
            ->andWhere('c.id <> ?6')
            ->setParameters(array(
                0 => $calc->getCity(),
                1 => $calc->getHeatedArea() * 0.8,
                2 => $calc->getHeatedArea() * 1.2,
                3 => $calc->getHeatingPower() * 0.8,
                4 => $calc->getHeatingPower() * 1.2,
                5 => $calc->getStoveType(),
                6 => $calc->getId(),
            ))
            ->getQuery()
            ->getResult();

        $resume = [];

        foreach ($comparables as $c) {
            $cost = $c->getFuelCost();

            if ($cost > 0) {
                $resume[] = $cost;
            }
        }

        $comment = '';

        if (count($resume) > 2) {
            $comment = sprintf('Średni koszt ogrzewania %d domów z twojej okolicy to %dzł', count($resume), array_sum($resume) / count($resume));
        }

        return $comment;
    }
}
