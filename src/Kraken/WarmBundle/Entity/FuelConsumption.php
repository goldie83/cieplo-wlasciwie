<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fuel_consumption")
 */
class FuelConsumption
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Calculation", inversedBy="fuel_consumptions", cascade={"persist"})
     * @ORM\JoinColumn(name="calculation_id", referencedColumnName="id", nullable=true)
     */
    protected $calculation;

    /**
     * @ORM\ManyToOne(targetEntity="Fuel", inversedBy="fuel_consumptions", cascade={"persist"})
     * @ORM\JoinColumn(name="fuel_id", referencedColumnName="id", nullable=true)
     */
    protected $fuel;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $consumption;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $cost;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set consumption.
     *
     * @param string $consumption
     *
     * @return FuelConsumption
     */
    public function setConsumption($consumption)
    {
        $this->consumption = $consumption;

        return $this;
    }

    /**
     * Get consumption.
     *
     * @return string
     */
    public function getConsumption()
    {
        return $this->consumption;
    }

    /**
     * Set cost.
     *
     * @param string $cost
     *
     * @return FuelConsumption
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost.
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set calculation.
     *
     * @param \Kraken\WarmBundle\Entity\Calculation $calculation
     *
     * @return FuelConsumption
     */
    public function setCalculation(\Kraken\WarmBundle\Entity\Calculation $calculation = null)
    {
        $this->calculation = $calculation;

        return $this;
    }

    /**
     * Get calculation.
     *
     * @return \Kraken\WarmBundle\Entity\Calculation
     */
    public function getCalculation()
    {
        return $this->calculation;
    }

    /**
     * Set fuel.
     *
     * @param \Kraken\WarmBundle\Entity\Fuel $fuel
     *
     * @return FuelConsumption
     */
    public function setFuel(\Kraken\WarmBundle\Entity\Fuel $fuel = null)
    {
        $this->fuel = $fuel;

        return $this;
    }

    /**
     * Get fuel.
     *
     * @return \Kraken\WarmBundle\Entity\Fuel
     */
    public function getFuel()
    {
        return $this->fuel;
    }
}
