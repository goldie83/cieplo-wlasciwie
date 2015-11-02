<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="boiler_powers")
 */
class BoilerPower
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Boiler", inversedBy="boilerPowers")
     */
    protected $boiler;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $power;

    /**
     * @ORM\ManyToOne(targetEntity="FuelType", inversedBy="boilerPowers")
     */
    protected $fuelType;

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
     * Set power
     *
     * @param string $power
     * @return BoilerPower
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Get power
     *
     * @return string 
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * Set boiler
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $boiler
     * @return BoilerPower
     */
    public function setBoiler(\Kraken\RankingBundle\Entity\Boiler $boiler = null)
    {
        $this->boiler = $boiler;

        return $this;
    }

    /**
     * Get boiler
     *
     * @return \Kraken\RankingBundle\Entity\Boiler 
     */
    public function getBoiler()
    {
        return $this->boiler;
    }

    /**
     * Set fuelType
     *
     * @param \Kraken\RankingBundle\Entity\FuelType $fuelType
     * @return BoilerPower
     */
    public function setFuelType(\Kraken\RankingBundle\Entity\FuelType $fuelType = null)
    {
        $this->fuelType = $fuelType;

        return $this;
    }

    /**
     * Get fuelType
     *
     * @return \Kraken\RankingBundle\Entity\FuelType 
     */
    public function getFuelType()
    {
        return $this->fuelType;
    }
}
