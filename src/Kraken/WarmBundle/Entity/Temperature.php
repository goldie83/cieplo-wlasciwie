<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kraken\WarmBundle\Repository\TemperatureRepository")
 * @ORM\Table(name="temperature")
 */
class Temperature
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
     * @ORM\Column(type="integer", length=2)
     */
    protected $month;

    /**
     * @ORM\Column(type="integer", length=2)
     */
    protected $day;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="temperatures", cascade={"all"})
     */
    protected $city;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set month.
     *
     * @param int $month
     *
     * @return Temperature
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month.
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set day.
     *
     * @param int $day
     *
     * @return Temperature
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day.
     *
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set value.
     *
     * @param float $value
     *
     * @return Temperature
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set city.
     *
     * @param \Kraken\WarmBundle\Entity\City $city
     *
     * @return Temperature
     */
    public function setCity(\Kraken\WarmBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return \Kraken\WarmBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }
}
