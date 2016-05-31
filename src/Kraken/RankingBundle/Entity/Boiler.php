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
     * @ORM\OneToMany(targetEntity="Review", mappedBy="boiler", cascade={"all"}, orphanRemoval=true)
     */
    protected $reviews;

    /**
     * @ORM\OneToMany(targetEntity="Experience", mappedBy="boiler", cascade={"all"}, orphanRemoval=true)
     */
    protected $experiences;

    /**
     * @ORM\OneToMany(targetEntity="BoilerPower", mappedBy="boiler", cascade={"all"}, orphanRemoval=true)
     */
    protected $boilerPowers;

    /**
     * @ORM\OneToMany(targetEntity="BoilerFuelType", mappedBy="boiler", cascade={"all"}, orphanRemoval=true)
     */
    protected $boilerFuelTypes;

    /**
     * @ORM\OneToMany(targetEntity="Notice", mappedBy="boiler", cascade={"all"}, orphanRemoval=true)
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
     * @ORM\Column(type="decimal", name="typical_model_power", precision=3, scale=1, nullable=true)
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
     * @ORM\Column(type="boolean", name="is_published")
     */
    protected $published = false;

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
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setNormClass($normClass)
    {
        $this->normClass = $normClass;

        return $this;
    }

    public function getNormClass()
    {
        return $this->normClass;
    }

    public function setTypicalModelPower($typicalModelPower)
    {
        $this->typicalModelPower = $typicalModelPower;

        return $this;
    }

    public function getTypicalModelPower()
    {
        return (double) $this->typicalModelPower == (int) $this->typicalModelPower ? $this->typicalModelPower : number_format($this->typicalModelPower, 1);
    }

    public function setTypicalModelExchanger($typicalModelExchanger)
    {
        $this->typicalModelExchanger = $typicalModelExchanger;

        return $this;
    }

    public function getTypicalModelExchanger()
    {
        return $this->typicalModelExchanger;
    }

    public function setTypicalModelCapacity($typicalModelCapacity)
    {
        $this->typicalModelCapacity = $typicalModelCapacity;

        return $this;
    }

    public function getTypicalModelCapacity()
    {
        return $this->typicalModelCapacity;
    }

    public function getTypicalModelCapacityInKilograms()
    {
        return round($this->typicalModelCapacity * 0.8);
    }

    public function setTypicalModelPrice($typicalModelPrice)
    {
        $this->typicalModelPrice = $typicalModelPrice;

        return $this;
    }

    public function getTypicalModelPrice()
    {
        return $this->typicalModelPrice;
    }

    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;

        return $this;
    }

    public function getWarranty()
    {
        return $this->warranty;
    }

    public function setHasWarrantyCatches($hasWarrantyCatches)
    {
        $this->hasWarrantyCatches = $hasWarrantyCatches;

        return $this;
    }

    public function getHasWarrantyCatches()
    {
        return $this->hasWarrantyCatches;
    }

    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setCategory(\Kraken\RankingBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setManufacturer(\Kraken\RankingBundle\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    public function addChange(\Kraken\RankingBundle\Entity\Change $changes)
    {
        $this->changes[] = $changes;

        return $this;
    }

    public function removeChange(\Kraken\RankingBundle\Entity\Change $changes)
    {
        $this->changes->removeElement($changes);
    }

    public function getChanges()
    {
        return $this->changes;
    }

    public function setChanges($changes)
    {
        $this->changes = $changes;

        return $this;
    }

    public function getNotices()
    {
        return $this->notices;
    }

    public function setNotices($notices)
    {
        $this->notices = $notices;
    }

    public function addBoilerPower(\Kraken\RankingBundle\Entity\BoilerPower $boilerPowers)
    {
        $this->boilerPowers[] = $boilerPowers;

        return $this;
    }

    public function removeBoilerPower(\Kraken\RankingBundle\Entity\BoilerPower $boilerPowers)
    {
        $this->boilerPowers->removeElement($boilerPowers);
    }

    public function getBoilerPowers()
    {
        return $this->boilerPowers;
    }

    public function setBoilerPowers($boilerPowers)
    {
        $this->boilerPowers = $boilerPowers;

        return $this;
    }

    public function addAcceptedFuelType(\Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes)
    {
        $this->acceptedFuelTypes[] = $acceptedFuelTypes;

        return $this;
    }

    public function removeAcceptedFuelType(\Kraken\RankingBundle\Entity\FuelType $acceptedFuelTypes)
    {
        $this->acceptedFuelTypes->removeElement($acceptedFuelTypes);
    }

    public function getAcceptedFuelTypes()
    {
        return $this->acceptedFuelTypes;
    }

    public function setLead($lead)
    {
        $this->lead = $lead;

        return $this;
    }

    public function getLead()
    {
        return $this->lead;
    }

    public function setRatingExplanation($ratingExplanation)
    {
        $this->ratingExplanation = $ratingExplanation;

        return $this;
    }

    public function getRatingExplanation()
    {
        return $this->ratingExplanation;
    }

    public function setUserManual($userManual)
    {
        $this->userManual = $userManual;

        return $this;
    }

    public function getUserManual()
    {
        return $this->userManual;
    }

    public function setForClosedSystem($forClosedSystem)
    {
        $this->forClosedSystem = $forClosedSystem;

        return $this;
    }

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
            if ($property->getValuation() == 'advantage') {
                $properties[] = $property;
            }
        }

        return $properties;
    }

    public function getNegativeNotices()
    {
        $properties = [];

        foreach ($this->notices as $property) {
            if ($property->getValuation() == 'disadvantage') {
                $properties[] = $property;
            }
        }

        return $properties;
    }

    public function getUnknownNotices()
    {
        $properties = [];

        foreach ($this->notices as $property) {
            if ($property->getValuation() == 'unknown') {
                $properties[] = $property;
            }
        }

        return $properties;
    }

    public function setMaterial($material)
    {
        $this->material = $material;

        return $this;
    }

    public function getMaterial()
    {
        return $this->material;
    }

    public function addNotice(\Kraken\RankingBundle\Entity\Notice $notices)
    {
        $this->notices[] = $notices;

        return $this;
    }

    public function removeNotice(\Kraken\RankingBundle\Entity\Notice $notices)
    {
        $this->notices->removeElement($notices);
    }

    public function addBoilerFuelType(\Kraken\RankingBundle\Entity\BoilerFuelType $boilerFuelTypes)
    {
        $this->boilerFuelTypes[] = $boilerFuelTypes;

        return $this;
    }

    public function removeBoilerFuelType(\Kraken\RankingBundle\Entity\BoilerFuelType $boilerFuelTypes)
    {
        $this->boilerFuelTypes->removeElement($boilerFuelTypes);
    }

    public function getBoilerFuelTypes()
    {
        return $this->boilerFuelTypes;
    }

    public function setBoilerFuelTypes($boilerFuelTypes)
    {
        $this->boilerFuelTypes = $boilerFuelTypes;

        return $this;
    }

    public function isHandFueled()
    {
        return $this->category->getParent() == 'Kotły zasypowe';
    }

    public function typicalModelWorkTime()
    {
        $fuelAmount = $this->getTypicalModelCapacityInKilograms();
        $fuelHeatingValue = 7.5;
        $averageEfficiency = 0.6;

        if ($fuelAmount && $this->typicalModelPower) {
            return round(($fuelAmount * $fuelHeatingValue * $averageEfficiency)/(0.6 * $this->typicalModelPower));
        }

        return 0;
    }

    public function setRejected($rejected)
    {
        $this->rejected = $rejected;

        return $this;
    }

    public function isRejected()
    {
        return $this->rejected;
    }

    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    public function isPublished()
    {
        return $this->published;
    }

    public function setManufacturerSite($manufacturerSite)
    {
        $this->manufacturerSite = $manufacturerSite;

        return $this;
    }

    public function getManufacturerSite()
    {
        return $this->manufacturerSite;
    }

    public function getRejected()
    {
        return $this->rejected;
    }

    public function setNeedsFixing($needsFixing)
    {
        $this->needsFixing = $needsFixing;

        return $this;
    }

    public function getNeedsFixing()
    {
        return $this->needsFixing;
    }

    public function addReview(Review $reviews)
    {
        $this->reviews[] = $reviews;

        return $this;
    }

    public function removeReview(Review $reviews)
    {
        $this->reviews->removeElement($reviews);
    }

    public function getReviews()
    {
        return $this->reviews;
    }
}
