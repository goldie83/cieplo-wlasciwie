<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fuel_types")
 */
class FuelType
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
    protected $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;

    /**
     * @ORM\OneToMany(targetEntity="BoilerPower", mappedBy="fuelType")
     */
    protected $boilerPowers;

    public function __construct()
    {
        $this->boilerPowers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return FuelType
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
     * Set slug
     *
     * @param string $slug
     * @return FuelType
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add boilerPowers
     *
     * @param \Kraken\RankingBundle\Entity\BoilerPower $boilerPowers
     * @return FuelType
     */
    public function addBoilerPower(\Kraken\RankingBundle\Entity\BoilerPower $boilerPowers)
    {
        $this->boilerPowers[] = $boilerPowers;

        return $this;
    }

    /**
     * Remove boilerPowers
     *
     * @param \Kraken\RankingBundle\Entity\BoilerPower $boilerPowers
     */
    public function removeBoilerPower(\Kraken\RankingBundle\Entity\BoilerPower $boilerPowers)
    {
        $this->boilerPowers->removeElement($boilerPowers);
    }

    /**
     * Get boilerPowers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoilerPowers()
    {
        return $this->boilerPowers;
    }
}
