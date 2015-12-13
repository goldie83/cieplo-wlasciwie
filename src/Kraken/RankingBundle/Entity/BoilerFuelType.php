<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="boiler_fuel_types")
 */
class BoilerFuelType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Boiler", inversedBy="boilerFuelTypes")
     * @ORM\JoinColumn(name="boiler_id", referencedColumnName="id", nullable=false)
     */
    protected $boiler;

    /**
     * @ORM\ManyToOne(targetEntity="FuelType", inversedBy="boilerFuelTypes")
     * @ORM\JoinColumn(name="fuel_type_id", referencedColumnName="id", nullable=false)
     */
    protected $fuelType;

    /**
     * @ORM\Column(type="boolean", name="is_primary")
     */
    protected $primary;

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
     * Set primary
     *
     * @param boolean $primary
     * @return BoilerFuelType
     */
    public function setPrimary($primary)
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * Get primary
     *
     * @return boolean
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * Set boiler
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $boiler
     * @return BoilerFuelType
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
     * @return BoilerFuelType
     */
    public function setFuelType(\Kraken\RankingBundle\Entity\FuelType $fuelType)
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
