<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fuel")
 */
class Fuel
{
    const TYPE_WOOD = 'wood';
    const TYPE_NATURAL_GAS = 'natural_gas';
    const TYPE_PROPANE = 'propane';
    const TYPE_COKE = 'coke';
    const TYPE_SAND_COAL = 'sand_coal';
    const TYPE_PELLET = 'pellet';
    const TYPE_ELECTRICITY = 'electricity';
    const TYPE_BROWN_COAL = 'brown_coal';
    const TYPE_COAL = 'bituminous_coal';
    const TYPE_ECO_COAL = 'eco_coal';
    const TYPE_NETWORK_HEAT = 'network_heat';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=3)
     */
    protected $unit;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(type="integer")
     */
    protected $trade_amount;

    /**
     * @ORM\Column(type="string", length=3)
     */
    protected $trade_unit;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    protected $energy;

    /**
     * @ORM\OneToMany(targetEntity="HeatingVariant", mappedBy="fuel")
     */
    protected $heatingVariants;

    /**
     * @ORM\OneToMany(targetEntity="FuelConsumption", mappedBy="fuel")
     */
    protected $fuel_consumptions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->heatingVariants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setTradeAmount($tradeAmount)
    {
        $this->trade_amount = $tradeAmount;

        return $this;
    }

    public function getTradeAmount()
    {
        return $this->trade_amount;
    }

    public function setTradeUnit($tradeUnit)
    {
        $this->trade_unit = $tradeUnit;

        return $this;
    }

    public function getTradeUnit()
    {
        return $this->trade_unit;
    }

    public function setEnergy($energy)
    {
        $this->energy = $energy;

        return $this;
    }

    public function getEnergy()
    {
        return $this->energy;
    }

    public function addHeatingVariant(\Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants)
    {
        $this->heatingVariants[] = $heatingVariants;

        return $this;
    }

    public function removeHeatingVariant(\Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants)
    {
        $this->heatingVariants->removeElement($heatingVariants);
    }

    public function getHeatingVariants()
    {
        return $this->heatingVariants;
    }

    public function addCalculation(\Kraken\WarmBundle\Entity\Calculation $calculations)
    {
        $this->calculations[] = $calculations;

        return $this;
    }

    public function removeCalculation(\Kraken\WarmBundle\Entity\Calculation $calculations)
    {
        $this->calculations->removeElement($calculations);
    }

    public function getCalculations()
    {
        return $this->calculations;
    }
}
