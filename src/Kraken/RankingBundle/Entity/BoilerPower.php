<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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


    public function __toString()
    {
        $power = (double) $this->power == (int) $this->power ? number_format($this->power) : number_format($this->power, 1);

        return $power . 'kW';
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    public function getPower()
    {
        return $this->power;
    }

    /**
     * Set boiler.
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $boiler
     *
     * @return BoilerPower
     */
    public function setBoiler(\Kraken\RankingBundle\Entity\Boiler $boiler)
    {
        $this->boiler = $boiler;

        return $this;
    }

    /**
     * Get boiler.
     *
     * @return \Kraken\RankingBundle\Entity\Boiler
     */
    public function getBoiler()
    {
        return $this->boiler;
    }
}
