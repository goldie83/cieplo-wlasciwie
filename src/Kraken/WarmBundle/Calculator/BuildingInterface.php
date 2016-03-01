<?php

namespace Kraken\WarmBundle\Calculator;

interface BuildingInterface
{
    /**
     * @return float
     */
    public function getEnergyLossToOutside();

    /**
     * @return float
     */
    public function getEnergyLossToUnheated();

    /**
     * @return \Kraken\WarmBundle\Entity\House|null
     */
    public function getHouse();
    public function getEnergyLossBreakdown();
}
