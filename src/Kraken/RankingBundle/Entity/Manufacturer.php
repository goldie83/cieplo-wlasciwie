<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="manufacturers")
 */
class Manufacturer
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
     * @ORM\Column(type="string")
     * @Assert\Url()
     */
    protected $website;

    /**
     * @ORM\OneToMany(targetEntity="Boiler", mappedBy="manufacturer")
     */
    protected $boilers;

    /**
     * @ORM\OneToMany(targetEntity="Search", mappedBy="manufacturer")
     */
    protected $searches;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->boilers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

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
     * Set name
     *
     * @param string $name
     * @return Manufacturer
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
     * Set website
     *
     * @param string $website
     * @return Manufacturer
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Add boilers
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $boilers
     * @return Manufacturer
     */
    public function addBoiler(\Kraken\RankingBundle\Entity\Boiler $boilers)
    {
        $this->boilers[] = $boilers;

        return $this;
    }

    /**
     * Remove boilers
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $boilers
     */
    public function removeBoiler(\Kraken\RankingBundle\Entity\Boiler $boilers)
    {
        $this->boilers->removeElement($boilers);
    }

    /**
     * Get boilers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoilers()
    {
        return $this->boilers;
    }

    /**
     * Add searches
     *
     * @param \Kraken\RankingBundle\Entity\Search $searches
     * @return Manufacturer
     */
    public function addSearch(\Kraken\RankingBundle\Entity\Search $searches)
    {
        $this->searches[] = $searches;

        return $this;
    }

    /**
     * Remove searches
     *
     * @param \Kraken\RankingBundle\Entity\Search $searches
     */
    public function removeSearch(\Kraken\RankingBundle\Entity\Search $searches)
    {
        $this->searches->removeElement($searches);
    }

    /**
     * Get searches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSearches()
    {
        return $this->searches;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Manufacturer
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
}
