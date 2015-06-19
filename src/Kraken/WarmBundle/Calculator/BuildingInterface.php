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
    public function getHeatedArea();
    public function getNumberOfWalls();

    /**
     * @return \Kraken\WarmBundle\Entity\House|null
     */
    public function getHouse();
    public function getEnergyLossBreakdown();
    public function getHouseDescription();
}
