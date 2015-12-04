<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="searches")
 */
class Search
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="string")
     */
    protected $modelName;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="searches")
     */
    protected $category;

    /**
     * @ORM\ManyToMany(targetEntity="FuelType", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="search_fuel_types",
     *      joinColumns={@ORM\JoinColumn(name="search_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fuel_type_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $fuelType;

    /**
     * @ORM\Column(type="string")
     */
    protected $power;

    /**
     * @ORM\Column(type="string")
     */
    protected $boilerClass;

    /**
     * @ORM\Column(type="string")
     */
    protected $rating;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fuelType = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set created
     *
     * @param \DateTime $created
     * @return Search
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modelName
     *
     * @param string $modelName
     * @return Search
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;

        return $this;
    }

    /**
     * Get modelName
     *
     * @return string
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * Set power
     *
     * @param string $power
     * @return Search
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
     * Set boilerClass
     *
     * @param string $boilerClass
     * @return Search
     */
    public function setBoilerClass($boilerClass)
    {
        $this->boilerClass = $boilerClass;

        return $this;
    }

    /**
     * Get boilerClass
     *
     * @return string
     */
    public function getBoilerClass()
    {
        return $this->boilerClass;
    }

    /**
     * Set rating
     *
     * @param string $rating
     * @return Search
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Add fuelType
     *
     * @param \Kraken\RankingBundle\Entity\FuelType $fuelType
     * @return Search
     */
    public function addFuelType(\Kraken\RankingBundle\Entity\FuelType $fuelType)
    {
        $this->fuelType[] = $fuelType;

        return $this;
    }

    /**
     * Remove fuelType
     *
     * @param \Kraken\RankingBundle\Entity\FuelType $fuelType
     */
    public function removeFuelType(\Kraken\RankingBundle\Entity\FuelType $fuelType)
    {
        $this->fuelType->removeElement($fuelType);
    }

    /**
     * Get fuelType
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFuelType()
    {
        return $this->fuelType;
    }

    /**
     * Set category
     *
     * @param \Kraken\RankingBundle\Entity\Category $category
     * @return Search
     */
    public function setCategory(\Kraken\RankingBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Kraken\RankingBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
