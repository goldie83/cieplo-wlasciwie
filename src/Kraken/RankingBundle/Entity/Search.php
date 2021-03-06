<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @ORM\Column(type="string", nullable=true, name="model_name")
     */
    protected $modelName;

    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer", inversedBy="searches")
     */
    protected $manufacturer;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="searches")
     */
    protected $category;

    /**
     * @ORM\ManyToMany(targetEntity="FuelType", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="search_fuel_types",
     *      joinColumns={@ORM\JoinColumn(name="search_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fuel_type_id", referencedColumnName="id")}
     *      )
     **/
    protected $fuelType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $power;

    /**
     * @ORM\Column(type="string", name="norm_class", nullable=true)
     */
    protected $normClass;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $ecodesign;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $rating;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $material;

    /**
     * @ORM\Column(type="boolean", name="is_for_closed_system", nullable=true)
     */
    protected $forClosedSystem;

    /**
     * @ORM\Column(type="boolean", name="needs_fixing", nullable=true)
     */
    protected $needsFixing = true;

    /**
     * @ORM\Column(type="boolean", name="is_rejected", nullable=true)
     */
    protected $rejected;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fuelType = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getUid()
    {
        return base_convert($this->id, 10, 36);
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Search
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modelName.
     *
     * @param string $modelName
     *
     * @return Search
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;

        return $this;
    }

    /**
     * Get modelName.
     *
     * @return string
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * Set power.
     *
     * @param string $power
     *
     * @return Search
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Get power.
     *
     * @return string
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * Set normClass.
     *
     * @param string $normClass
     *
     * @return Search
     */
    public function setNormClass($normClass)
    {
        $this->normClass = $normClass;

        return $this;
    }

    /**
     * Get normClass.
     *
     * @return string
     */
    public function getNormClass()
    {
        return $this->normClass;
    }

    public function setEcodesign($ecodesign)
    {
        $this->ecodesign = $ecodesign;

        return $this;
    }

    public function isEcodesign()
    {
        return $this->ecodesign;
    }

    /**
     * Set rating.
     *
     * @param string $rating
     *
     * @return Search
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating.
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Add fuelType.
     *
     * @param \Kraken\RankingBundle\Entity\FuelType $fuelType
     *
     * @return Search
     */
    public function addFuelType(\Kraken\RankingBundle\Entity\FuelType $fuelType)
    {
        $this->fuelType[] = $fuelType;

        return $this;
    }

    /**
     * Remove fuelType.
     *
     * @param \Kraken\RankingBundle\Entity\FuelType $fuelType
     */
    public function removeFuelType(\Kraken\RankingBundle\Entity\FuelType $fuelType)
    {
        $this->fuelType->removeElement($fuelType);
    }

    /**
     * Get fuelType.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFuelType()
    {
        return $this->fuelType;
    }

    /**
     * Set category.
     *
     * @param \Kraken\RankingBundle\Entity\Category $category
     *
     * @return Search
     */
    public function setCategory(\Kraken\RankingBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return \Kraken\RankingBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set forClosedSystem.
     *
     * @param bool $forClosedSystem
     *
     * @return Search
     */
    public function setForClosedSystem($forClosedSystem)
    {
        $this->forClosedSystem = $forClosedSystem;

        return $this;
    }

    /**
     * Get forClosedSystem.
     *
     * @return bool
     */
    public function isForClosedSystem()
    {
        return $this->forClosedSystem;
    }

    public function isEmpty()
    {
        return $this->modelName == ''
            && $this->manufacturer == ''
            && $this->category == ''
            && $this->fuelType->count() == 0
            && $this->power == ''
            && $this->normClass == ''
            && $this->rating == ''
            && $this->material == ''
            && $this->forClosedSystem == false
            && $this->needsFixing == false;
    }

    public function isOnlyCategorySelected()
    {
        return $this->modelName == ''
            && $this->manufacturer == ''
            && $this->category != ''
            && $this->fuelType->count() == 0
            && $this->power == ''
            && $this->normClass == ''
            && $this->rating == ''
            && $this->material == ''
            && $this->forClosedSystem == false
            && $this->needsFixing == false;
    }

    public function isOnlyManufacturerSelected()
    {
        return $this->modelName == ''
            && $this->manufacturer != ''
            && $this->category == ''
            && $this->fuelType->count() == 0
            && $this->power == ''
            && $this->normClass == ''
            && $this->rating == ''
            && $this->material == ''
            && $this->forClosedSystem == false
            && $this->needsFixing == false;
    }

    /**
     * Get forClosedSystem.
     *
     * @return bool
     */
    public function getForClosedSystem()
    {
        return $this->forClosedSystem;
    }

    /**
     * Set manufacturer.
     *
     * @param \Kraken\RankingBundle\Entity\Manufacturer $manufacturer
     *
     * @return Search
     */
    public function setManufacturer(\Kraken\RankingBundle\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer.
     *
     * @return \Kraken\RankingBundle\Entity\Manufacturer
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set material.
     *
     * @param string $material
     *
     * @return Search
     */
    public function setMaterial($material)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material.
     *
     * @return string
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Set needsFixing.
     *
     * @param bool $needsFixing
     *
     * @return Search
     */
    public function setNeedsFixing($needsFixing)
    {
        $this->needsFixing = $needsFixing;

        return $this;
    }

    /**
     * Get needsFixing.
     *
     * @return bool
     */
    public function needsFixing()
    {
        return $this->needsFixing;
    }

    /**
     * Set rejected.
     *
     * @param bool $rejected
     *
     * @return Search
     */
    public function setRejected($rejected)
    {
        $this->rejected = $rejected;

        return $this;
    }

    /**
     * Get rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->rejected;
    }

    /**
     * Get rejected.
     *
     * @return bool
     */
    public function getRejected()
    {
        return $this->rejected;
    }
}
