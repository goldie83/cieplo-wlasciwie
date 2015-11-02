<?php

namespace Kraken\RankingBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="boilers")
 */
class Boiler
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
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Image()
     */
    protected $image;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Image()
     */
    protected $crossSection;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="boilers")
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer", inversedBy="boilers")
     */
    protected $manufacturer;

    /**
     * @ORM\OneToMany(targetEntity="Change", mappedBy="boiler")
     */
    protected $changes;

    /**
     * @ORM\OneToMany(targetEntity="BoilerProperty", mappedBy="boiler")
     */
    protected $boilerProperties;

    /**
     * @ORM\OneToMany(targetEntity="BoilerPower", mappedBy="boiler")
     */
    protected $boilerPowers;

    /**
     * @ORM\ManyToMany(targetEntity="FuelType")
     * @ORM\JoinTable(name="boiler_fuel_types",
     *      joinColumns={@ORM\JoinColumn(name="boiler_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fuel_type_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $acceptedFuelTypes;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    protected $rating;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $normClass;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    protected $typicalModelPower;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    protected $typicalModelExchanger;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    protected $typicalModelCapacity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $typicalModelPrice;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    protected $warrantyYears;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $hasWarrantyCatches = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $forClosedSystem = false;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->changes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->boilerProperties = new \Doctrine\Common\Collections\ArrayCollection();
        $this->boilerPowers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->acceptedFuelTypes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Boiler
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
     * @return Boiler
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
     * Set image
     *
     * @param string $image
     * @return Boiler
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set crossSection
     *
     * @param string $crossSection
     * @return Boiler
     */
    public function setCrossSection($crossSection)
    {
        $this->crossSection = $crossSection;

        return $this;
    }

    /**
     * Get crossSection
     *
     * @return string
     */
    public function getCrossSection()
    {
        return $this->crossSection;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Boiler
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set rating
     *
     * @param string $rating
     * @return Boiler
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
     * Set normClass
     *
     * @param string $normClass
     * @return Boiler
     */
    public function setNormClass($normClass)
    {
        $this->normClass = $normClass;

        return $this;
    }

    /**
     * Get normClass
     *
     * @return string
     */
    public function getNormClass()
    {
        return $this->normClass;
    }

    /**
     * Set typicalModelPower
     *
     * @param string $typicalModelPower
     * @return Boiler
     */
    public function setTypicalModelPower($typicalModelPower)
    {
        $this->typicalModelPower = $typicalModelPower;

        return $this;
    }

    /**
     * Get typicalModelPower
     *
     * @return string
     */
    public function getTypicalModelPower()
    {
        return $this->typicalModelPower;
    }

    /**
     * Set typicalModelExchanger
     *
     * @param string $typicalModelExchanger
     * @return Boiler
     */
    public function setTypicalModelExchanger($typicalModelExchanger)
    {
        $this->typicalModelExchanger = $typicalModelExchanger;

        return $this;
    }

    /**
     * Get typicalModelExchanger
     *
     * @return string
     */
    public function getTypicalModelExchanger()
    {
        return $this->typicalModelExchanger;
    }

    /**
     * Set typicalModelCapacity
     *
     * @param string $typicalModelCapacity
     * @return Boiler
     */
    public function setTypicalModelCapacity($typicalModelCapacity)
    {
        $this->typicalModelCapacity = $typicalModelCapacity;

        return $this;
    }

    /**
     * Get typicalModelCapacity
     *
     * @return string
     */
    public function getTypicalModelCapacity()
    {
        return $this->typicalModelCapacity;
    }

    /**
     * Set typicalModelPrice
     *
     * @param integer $typicalModelPrice
     * @return Boiler
     */
    public function setTypicalModelPrice($typicalModelPrice)
    {
        $this->typicalModelPrice = $typicalModelPrice;

        return $this;
    }

    /**
     * Get typicalModelPrice
     *
     * @return integer
     */
    public function getTypicalModelPrice()
    {
        return $this->typicalModelPrice;
    }

    /**
     * Set warrantyYears
     *
     * @param string $warrantyYears
     * @return Boiler
     */
    public function setWarrantyYears($warrantyYears)
    {
        $this->warrantyYears = $warrantyYears;

        return $this;
    }

    /**
     * Get warrantyYears
     *
     * @return string
     */
    public function getWarrantyYears()
    {
        return $this->warrantyYears;
    }

    /**
     * Set hasWarrantyCatches
     *
     * @param boolean $hasWarrantyCatches
     * @return Boiler
     */
    public function setHasWarrantyCatches($hasWarrantyCatches)
    {
        $this->hasWarrantyCatches = $hasWarrantyCatches;

        return $this;
    }

    /**
     * Get hasWarrantyCatches
     *
     * @return boolean
     */
    public function getHasWarrantyCatches()
    {
        return $this->hasWarrantyCatches;
    }

    /**
     * Set forClosedSystem
     *
     * @param boolean $forClosedSystem
     * @return Boiler
     */
    public function setForClosedSystem($forClosedSystem)
    {
        $this->forClosedSystem = $forClosedSystem;

        return $this;
    }

    /**
     * Get forClosedSystem
     *
     * @return boolean
     */
    public function getForClosedSystem()
    {
        return $this->forClosedSystem;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Boiler
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Boiler
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set category
     *
     * @param \Kraken\RankingBundle\Entity\Category $category
     * @return Boiler
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

    /**
     * Set manufacturer
     *
     * @param \Kraken\RankingBundle\Entity\Manufacturer $manufacturer
     * @return Boiler
     */
    public function setManufacturer(\Kraken\RankingBundle\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return \Kraken\RankingBundle\Entity\Manufacturer
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Add changes
     *
     * @param \Kraken\RankingBundle\Entity\Change $changes
     * @return Boiler
     */
    public function addChange(\Kraken\RankingBundle\Entity\Change $changes)
    {
        $this->changes[] = $changes;

        return $this;
    }

    /**
     * Remove changes
     *
     * @param \Kraken\RankingBundle\Entity\Change $changes
     */
    public function removeChange(\Kraken\RankingBundle\Entity\Change $changes)
    {
        $this->changes->removeElement($changes);
    }

    /**
     * Get changes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Add boilerProperties
     *
     * @param \Kraken\RankingBundle\Entity\BoilerProperty $boilerProperties
     * @return Boiler
     */
    public function addBoilerProperty(\Kraken\RankingBundle\Entity\BoilerProperty $boilerProperties)
    {
        $this->boilerProperties[] = $boilerProperties;

        return $this;
    }

    /**
     * Remove boilerProperties
     *
     * @param \Kraken\RankingBundle\Entity\BoilerProperty $boilerProperties
     */
    public function removeBoilerProperty(\Kraken\RankingBundle\Entity\BoilerProperty $boilerProperties)
    {
        $this->boilerProperties->removeElement($boilerProperties);
    }

    /**
     * Get boilerProperties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoilerProperties()
    {
        return $this->boilerProperties;
    }

    /**
     * Add boilerPowers
     *
     * @param \Kraken\RankingBundle\Entity\BoilerPower $boilerPowers
     * @return Boiler
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

    /**
     * Add acceptedFuelTypes
     *
     * @param \Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes
     * @return Boiler
     */
    public function addAcceptedFuelType(\Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes)
    {
        $this->acceptedFuelTypes[] = $acceptedFuelTypes;

        return $this;
    }

    /**
     * Remove acceptedFuelTypes
     *
     * @param \Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes
     */
    public function removeAcceptedFuelType(\Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes)
    {
        $this->acceptedFuelTypes->removeElement($acceptedFuelTypes);
    }

    /**
     * Get acceptedFuelTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAcceptedFuelTypes()
    {
        return $this->acceptedFuelTypes;
    }
}
