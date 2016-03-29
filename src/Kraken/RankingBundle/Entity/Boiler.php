<?php

namespace Kraken\RankingBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="boilers")
 * @Vich\Uploadable
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
     * @Assert\File(
     *     maxSize="5M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="cover_image", fileNameProperty="image")
     */
    protected $imageFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $image;

    /**
     * @Assert\File(
     *     maxSize="5M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="cross_section", fileNameProperty="crossSection")
     */
    protected $crossSectionFile;

    /**
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\OneToMany(targetEntity="Change", mappedBy="boiler", cascade={"all"}, orphanRemoval=true)
     */
    protected $changes;

    /**
     * @ORM\ManyToMany(targetEntity="PropertyValue", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="boiler_property_values",
     *      joinColumns={@ORM\JoinColumn(name="boiler_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="property_value_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $propertyValues;

    /**
     * @ORM\OneToMany(targetEntity="BoilerPower", mappedBy="boiler", cascade={"all"}, orphanRemoval=true)
     */
    protected $boilerPowers;

    /**
     * @ORM\OneToMany(targetEntity="BoilerFuelType", mappedBy="boiler", cascade={"all"}, orphanRemoval=true)
     */
    protected $boilerFuelTypes;

    /**
     * @ORM\ManyToMany(targetEntity="Notice", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="boiler_notices",
     *      joinColumns={@ORM\JoinColumn(name="boiler_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="notice_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $notices;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $lead;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="text", name="rating_explanation", nullable=true)
     */
    protected $ratingExplanation;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    protected $rating;

    /**
     * @ORM\Column(type="string", name="norm_class", length=1, nullable=true)
     */
    protected $normClass;

    /**
     * @ORM\Column(type="decimal", name="typical_model_power", precision=4, scale=2, nullable=true)
     */
    protected $typicalModelPower;

    /**
     * @ORM\Column(type="decimal", name="typical_model_exchanger", precision=4, scale=2, nullable=true)
     */
    protected $typicalModelExchanger;

    /**
     * @ORM\Column(type="decimal", name="typical_model_capacity", nullable=true)
     */
    protected $typicalModelCapacity;

    /**
     * @ORM\Column(type="integer", name="typical_model_price", nullable=true)
     */
    protected $typicalModelPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $warranty;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $userManual;

    /**
     * @ORM\Column(type="string", name="manufacturer_site", nullable=true)
     */
    protected $manufacturerSite;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $material = 'steel';

    /**
     * @ORM\Column(type="boolean", name="is_for_closed_system")
     */
    protected $forClosedSystem = false;

    /**
     * @ORM\Column(type="boolean", name="needs_fixing")
     */
    protected $needsFixing = false;

    /**
     * @ORM\Column(type="boolean", name="is_rejected")
     */
    protected $rejected = false;

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
     * Constructor.
     */
    public function __construct()
    {
        $this->changes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->boilerPowers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->boilerFuelTypes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Boiler
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Boiler
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setCrossSectionFile(File $crossSection = null)
    {
        $this->crossSectionFile = $crossSection;

        if ($crossSection) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getCrossSectionFile()
    {
        return $this->crossSectionFile;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return Boiler
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set crossSection.
     *
     * @param string $crossSection
     *
     * @return Boiler
     */
    public function setCrossSection($crossSection)
    {
        $this->crossSection = $crossSection;

        return $this;
    }

    /**
     * Get crossSection.
     *
     * @return string
     */
    public function getCrossSection()
    {
        return $this->crossSection;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Boiler
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set rating.
     *
     * @param string $rating
     *
     * @return Boiler
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
     * Set normClass.
     *
     * @param string $normClass
     *
     * @return Boiler
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

    /**
     * Set typicalModelPower.
     *
     * @param string $typicalModelPower
     *
     * @return Boiler
     */
    public function setTypicalModelPower($typicalModelPower)
    {
        $this->typicalModelPower = $typicalModelPower;

        return $this;
    }

    /**
     * Get typicalModelPower.
     *
     * @return string
     */
    public function getTypicalModelPower()
    {
        return $this->typicalModelPower;
    }

    /**
     * Set typicalModelExchanger.
     *
     * @param string $typicalModelExchanger
     *
     * @return Boiler
     */
    public function setTypicalModelExchanger($typicalModelExchanger)
    {
        $this->typicalModelExchanger = $typicalModelExchanger;

        return $this;
    }

    /**
     * Get typicalModelExchanger.
     *
     * @return string
     */
    public function getTypicalModelExchanger()
    {
        return $this->typicalModelExchanger;
    }

    /**
     * Set typicalModelCapacity.
     *
     * @param string $typicalModelCapacity
     *
     * @return Boiler
     */
    public function setTypicalModelCapacity($typicalModelCapacity)
    {
        $this->typicalModelCapacity = $typicalModelCapacity;

        return $this;
    }

    /**
     * Get typicalModelCapacity.
     *
     * @return string
     */
    public function getTypicalModelCapacity()
    {
        return $this->typicalModelCapacity;
    }

    /**
     * Set typicalModelPrice.
     *
     * @param int $typicalModelPrice
     *
     * @return Boiler
     */
    public function setTypicalModelPrice($typicalModelPrice)
    {
        $this->typicalModelPrice = $typicalModelPrice;

        return $this;
    }

    /**
     * Get typicalModelPrice.
     *
     * @return int
     */
    public function getTypicalModelPrice()
    {
        return $this->typicalModelPrice;
    }

    /**
     * Set warranty.
     *
     * @param string $warranty
     *
     * @return Boiler
     */
    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;

        return $this;
    }

    /**
     * Get warranty.
     *
     * @return string
     */
    public function getWarranty()
    {
        return $this->warranty;
    }

    /**
     * Set hasWarrantyCatches.
     *
     * @param bool $hasWarrantyCatches
     *
     * @return Boiler
     */
    public function setHasWarrantyCatches($hasWarrantyCatches)
    {
        $this->hasWarrantyCatches = $hasWarrantyCatches;

        return $this;
    }

    /**
     * Get hasWarrantyCatches.
     *
     * @return bool
     */
    public function getHasWarrantyCatches()
    {
        return $this->hasWarrantyCatches;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Boiler
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
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return Boiler
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set category.
     *
     * @param \Kraken\RankingBundle\Entity\Category $category
     *
     * @return Boiler
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
     * Set manufacturer.
     *
     * @param \Kraken\RankingBundle\Entity\Manufacturer $manufacturer
     *
     * @return Boiler
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
     * Add changes.
     *
     * @param \Kraken\RankingBundle\Entity\Change $changes
     *
     * @return Boiler
     */
    public function addChange(\Kraken\RankingBundle\Entity\Change $changes)
    {
        $this->changes[] = $changes;

        return $this;
    }

    /**
     * Remove changes.
     *
     * @param \Kraken\RankingBundle\Entity\Change $changes
     */
    public function removeChange(\Kraken\RankingBundle\Entity\Change $changes)
    {
        $this->changes->removeElement($changes);
    }

    /**
     * Get changes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Add notices.
     *
     * @param \Kraken\RankingBundle\Entity\BoilerProperty $notices
     *
     * @return Boiler
     */
    public function addBoilerProperty(\Kraken\RankingBundle\Entity\BoilerProperty $notices)
    {
        $this->notices[] = $notices;

        return $this;
    }

    /**
     * Remove notices.
     *
     * @param \Kraken\RankingBundle\Entity\BoilerProperty $notices
     */
    public function removeBoilerProperty(\Kraken\RankingBundle\Entity\BoilerProperty $notices)
    {
        $this->notices->removeElement($notices);
    }

    /**
     * Get notices.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotices()
    {
        return $this->notices;
    }

    /**
     * Add boilerPowers.
     *
     * @param \Kraken\RankingBundle\Entity\BoilerPower $boilerPowers
     *
     * @return Boiler
     */
    public function addBoilerPower(\Kraken\RankingBundle\Entity\BoilerPower $boilerPowers)
    {
        $this->boilerPowers[] = $boilerPowers;

        return $this;
    }

    /**
     * Remove boilerPowers.
     *
     * @param \Kraken\RankingBundle\Entity\BoilerPower $boilerPowers
     */
    public function removeBoilerPower(\Kraken\RankingBundle\Entity\BoilerPower $boilerPowers)
    {
        $this->boilerPowers->removeElement($boilerPowers);
    }

    /**
     * Get boilerPowers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoilerPowers()
    {
        return $this->boilerPowers;
    }

    /**
     * Add acceptedFuelTypes.
     *
     * @param \Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes
     *
     * @return Boiler
     */
    public function addAcceptedFuelType(\Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes)
    {
        $this->acceptedFuelTypes[] = $acceptedFuelTypes;

        return $this;
    }

    /**
     * Remove acceptedFuelTypes.
     *
     * @param \Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes
     */
    public function removeAcceptedFuelType(\Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes)
    {
        $this->acceptedFuelTypes->removeElement($acceptedFuelTypes);
    }

    /**
     * Get acceptedFuelTypes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAcceptedFuelTypes()
    {
        return $this->acceptedFuelTypes;
    }

    /**
     * Set lead.
     *
     * @param string $lead
     *
     * @return Boiler
     */
    public function setLead($lead)
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * Get lead.
     *
     * @return string
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * Set ratingExplanation.
     *
     * @param string $ratingExplanation
     *
     * @return Boiler
     */
    public function setRatingExplanation($ratingExplanation)
    {
        $this->ratingExplanation = $ratingExplanation;

        return $this;
    }

    /**
     * Get ratingExplanation.
     *
     * @return string
     */
    public function getRatingExplanation()
    {
        return $this->ratingExplanation;
    }

    /**
     * Set userManual.
     *
     * @param string $userManual
     *
     * @return Boiler
     */
    public function setUserManual($userManual)
    {
        $this->userManual = $userManual;

        return $this;
    }

    /**
     * Get userManual.
     *
     * @return string
     */
    public function getUserManual()
    {
        return $this->userManual;
    }

    /**
     * Set forClosedSystem.
     *
     * @param bool $forClosedSystem
     *
     * @return Boiler
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
    public function getForClosedSystem()
    {
        return $this->forClosedSystem;
    }

    public function hasCrossSectionImage()
    {
        return $this->crossSection != '';
    }

    public function getWarrantyYears()
    {
        if ($this->warranty == 12) {
            return 'rok';
        }

        if ($this->warranty % 12 == 0) {
            $years = $this->warranty / 12;

            if ($years < 5) {
                return sprintf('%d lata', $years);
            }

            return sprintf('%d lat', $years);
        } else {
            $years = $this->warranty / 12;
            $fraction = ($this->warranty % 12) / 12 * 10;

            return sprintf('%d,%d roku', $years, $fraction);
        }
    }

    public function getExchangerNormPercent()
    {
        if (!$this->typicalModelPower || !$this->typicalModelExchanger) {
            return 0;
        }

        return ($this->typicalModelExchanger / $this->typicalModelPower) / 0.125 * 100;
    }

    public function getPositiveNotices()
    {
        $properties = [];

        foreach ($this->notices as $property) {
            if ($property->getType() == 'advantage') {
                $properties[] = $property;
            }
        }

        return $properties;
    }

    public function getNegativeNotices()
    {
        $properties = [];

        foreach ($this->notices as $property) {
            if ($property->getType() == 'disadvantage') {
                $properties[] = $property;
            }
        }

        return $properties;
    }

    public function getUnknownNotices()
    {
        $properties = [];

        foreach ($this->notices as $property) {
            if ($property->getType() == 'unknown') {
                $properties[] = $property;
            }
        }

        return $properties;
    }

    /**
     * Set material.
     *
     * @param string $material
     *
     * @return Boiler
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
     * Add propertyValues.
     *
     * @param \Kraken\RankingBundle\Entity\PropertyValue $propertyValues
     *
     * @return Boiler
     */
    public function addPropertyValue(\Kraken\RankingBundle\Entity\PropertyValue $propertyValues)
    {
        $this->propertyValues[] = $propertyValues;

        return $this;
    }

    /**
     * Remove propertyValues.
     *
     * @param \Kraken\RankingBundle\Entity\PropertyValue $propertyValues
     */
    public function removePropertyValue(\Kraken\RankingBundle\Entity\PropertyValue $propertyValues)
    {
        $this->propertyValues->removeElement($propertyValues);
    }

    /**
     * Get propertyValues.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropertyValues()
    {
        return $this->propertyValues;
    }

    /**
     * Add notices.
     *
     * @param \Kraken\RankingBundle\Entity\Notice $notices
     *
     * @return Boiler
     */
    public function addNotice(\Kraken\RankingBundle\Entity\Notice $notices)
    {
        $this->notices[] = $notices;

        return $this;
    }

    /**
     * Remove notices.
     *
     * @param \Kraken\RankingBundle\Entity\Notice $notices
     */
    public function removeNotice(\Kraken\RankingBundle\Entity\Notice $notices)
    {
        $this->notices->removeElement($notices);
    }

    /**
     * Add boilerFuelTypes.
     *
     * @param \Kraken\RankingBundle\Entity\BoilerFuelType $boilerFuelTypes
     *
     * @return Boiler
     */
    public function addBoilerFuelType(\Kraken\RankingBundle\Entity\BoilerFuelType $boilerFuelTypes)
    {
        $this->boilerFuelTypes[] = $boilerFuelTypes;

        return $this;
    }

    /**
     * Remove boilerFuelTypes.
     *
     * @param \Kraken\RankingBundle\Entity\BoilerFuelType $boilerFuelTypes
     */
    public function removeBoilerFuelType(\Kraken\RankingBundle\Entity\BoilerFuelType $boilerFuelTypes)
    {
        $this->boilerFuelTypes->removeElement($boilerFuelTypes);
    }

    /**
     * Get boilerFuelTypes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoilerFuelTypes()
    {
        return $this->boilerFuelTypes;
    }

    public function isHandFueled()
    {
        return $this->category->getParent() == 'Kotły zasypowe';
    }

    public function typicalModelWorkTime()
    {
        return 8;//TODO
    }

    /**
     * Set rejected.
     *
     * @param bool $rejected
     *
     * @return Boiler
     */
    public function setRejected($rejected)
    {
        $this->rejected = $rejected;

        return $this;
    }

    public function isRejected()
    {
        return $this->rejected;
    }

    /**
     * Set manufacturerSite.
     *
     * @param string $manufacturerSite
     *
     * @return Boiler
     */
    public function setManufacturerSite($manufacturerSite)
    {
        $this->manufacturerSite = $manufacturerSite;

        return $this;
    }

    /**
     * Get manufacturerSite.
     *
     * @return string
     */
    public function getManufacturerSite()
    {
        return $this->manufacturerSite;
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

    /**
     * Set needsFixing.
     *
     * @param bool $needsFixing
     *
     * @return Boiler
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
    public function getNeedsFixing()
    {
        return $this->needsFixing;
    }
}
