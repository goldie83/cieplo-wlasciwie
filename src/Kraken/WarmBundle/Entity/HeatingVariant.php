<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="heating_variant")
 */
class HeatingVariant
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Fuel", inversedBy="heatingVariants", cascade={"persist"})
     */
    protected $fuel;

    /**
     * @ORM\ManyToOne(targetEntity="HeatingDevice", inversedBy="heatingVariants", cascade={"persist"})
     */
    protected $heatingDevice;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=2)
     */
    protected $efficiency;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $detail;

    /**
     * @ORM\Column(type="integer")
     */
    protected $setup_cost;

    /**
     * @ORM\Column(type="integer")
     */
    protected $maintenance_time;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $legacy = false;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set efficiency
     *
     * @param string $efficiency
     * @return HeatingVariant
     */
    public function setEfficiency($efficiency)
    {
        $this->efficiency = $efficiency;

        return $this;
    }

    /**
     * Get efficiency
     *
     * @return string 
     */
    public function getEfficiency()
    {
        return $this->efficiency;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return HeatingVariant
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fuel
     *
     * @param \Kraken\WarmBundle\Entity\Fuel $fuel
     * @return HeatingVariant
     */
    public function setFuel(\Kraken\WarmBundle\Entity\Fuel $fuel = null)
    {
        $this->fuel = $fuel;

        return $this;
    }

    /**
     * Get fuel
     *
     * @return \Kraken\WarmBundle\Entity\Fuel 
     */
    public function getFuel()
    {
        return $this->fuel;
    }

    /**
     * Set heatingDevice
     *
     * @param \Kraken\WarmBundle\Entity\HeatingDevice $heatingDevice
     * @return HeatingVariant
     */
    public function setHeatingDevice(\Kraken\WarmBundle\Entity\HeatingDevice $heatingDevice = null)
    {
        $this->heatingDevice = $heatingDevice;

        return $this;
    }

    /**
     * Get heatingDevice
     *
     * @return \Kraken\WarmBundle\Entity\HeatingDevice 
     */
    public function getHeatingDevice()
    {
        return $this->heatingDevice;
    }

    public function getType()
    {
        return $this->fuel->getType() . '_' . $this->heatingDevice->getType();
    }

    /**
     * Set detail
     *
     * @param string $detail
     * @return HeatingVariant
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string 
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set setup_cost
     *
     * @param integer $setupCost
     * @return HeatingVariant
     */
    public function setSetupCost($setupCost)
    {
        $this->setup_cost = $setupCost;

        return $this;
    }

    /**
     * Get setup_cost
     *
     * @return integer 
     */
    public function getSetupCost()
    {
        return $this->setup_cost;
    }

    /**
     * Set maintenance_time
     *
     * @param integer $maintenanceTime
     * @return HeatingVariant
     */
    public function setMaintenanceTime($maintenanceTime)
    {
        $this->maintenance_time = $maintenanceTime;

        return $this;
    }

    /**
     * Get maintenance_time
     *
     * @return integer 
     */
    public function getMaintenanceTime()
    {
        return $this->maintenance_time;
    }

    /**
     * Set legacy
     *
     * @param boolean $legacy
     * @return HeatingVariant
     */
    public function setLegacy($legacy)
    {
        $this->legacy = $legacy;

        return $this;
    }

    /**
     * Get legacy
     *
     * @return boolean 
     */
    public function isLegacy()
    {
        return $this->legacy;
    }
}
