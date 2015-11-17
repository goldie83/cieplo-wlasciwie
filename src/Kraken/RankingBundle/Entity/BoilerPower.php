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
     * @ORM\JoinColumn(name="boiler_id", referencedColumnName="id", nullable=false)
     */
    protected $boiler;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    protected $power;

    /**
     * @ORM\ManyToOne(targetEntity="FuelType", inversedBy="boilerPowers")
     * @ORM\JoinColumn(name="fuel_type_id", referencedColumnName="id", nullable=false)
     */
    protected $fuelType;

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
    public function setBoiler(\Kraken\RankingBundle\Entity\Boiler $boiler)
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
