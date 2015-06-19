<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fuel")
 */
class Fuel
{
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
     * @ORM\OneToMany(targetEntity="Calculation", mappedBy="fuel")
     */
    protected $calculations;

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
     * Set type.
     *
     * @param string $type
     *
     * @return Fuel
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Fuel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set unit.
     *
     * @param string $unit
     *
     * @return Fuel
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return Fuel
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set trade_amount.
     *
     * @param int $tradeAmount
     *
     * @return Fuel
     */
    public function setTradeAmount($tradeAmount)
    {
        $this->trade_amount = $tradeAmount;

        return $this;
    }

    /**
     * Get trade_amount.
     *
     * @return int
     */
    public function getTradeAmount()
    {
        return $this->trade_amount;
    }

    /**
     * Set trade_unit.
     *
     * @param string $tradeUnit
     *
     * @return Fuel
     */
    public function setTradeUnit($tradeUnit)
    {
        $this->trade_unit = $tradeUnit;

        return $this;
    }

    /**
     * Get trade_unit.
     *
     * @return string
     */
    public function getTradeUnit()
    {
        return $this->trade_unit;
    }

    /**
     * Set energy.
     *
     * @param string $energy
     *
     * @return Fuel
     */
    public function setEnergy($energy)
    {
        $this->energy = $energy;

        return $this;
    }

    /**
     * Get energy.
     *
     * @return string
     */
    public function getEnergy()
    {
        return $this->energy;
    }

    /**
     * Add heatingVariants.
     *
     * @param \Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants
     *
     * @return Fuel
     */
    public function addHeatingVariant(\Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants)
    {
        $this->heatingVariants[] = $heatingVariants;

        return $this;
    }

    /**
     * Remove heatingVariants.
     *
     * @param \Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants
     */
    public function removeHeatingVariant(\Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants)
    {
        $this->heatingVariants->removeElement($heatingVariants);
    }

    /**
     * Get heatingVariants.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHeatingVariants()
    {
        return $this->heatingVariants;
    }

    /**
     * Add calculations.
     *
     * @param \Kraken\WarmBundle\Entity\Calculation $calculations
     *
     * @return Fuel
     */
    public function addCalculation(\Kraken\WarmBundle\Entity\Calculation $calculations)
    {
        $this->calculations[] = $calculations;

        return $this;
    }

    /**
     * Remove calculations.
     *
     * @param \Kraken\WarmBundle\Entity\Calculation $calculations
     */
    public function removeCalculation(\Kraken\WarmBundle\Entity\Calculation $calculations)
    {
        $this->calculations->removeElement($calculations);
    }

    /**
     * Get calculations.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalculations()
    {
        return $this->calculations;
    }
}
