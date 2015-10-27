<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Image()
     */
    protected $image;

    /**
     * @ORM\Column(type="string")
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
     *      joinColumns={@JoinColumn(name="boiler_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="fuel_type_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $acceptedFuelTypes;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="string", length=2)
     */
    protected $rating;

    /**
     * @ORM\Column(type="string", length=1)
     */
    protected $normClass;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $typicalModelPower;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $typicalModelExchanger;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $typicalModelCapacity;

    /**
     * @ORM\Column(type="integer")
     */
    protected $typicalModelPrice;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $warrantyYears;

    /**
     * @ORM\Column(type="boolean", default=false)
     */
    protected $hasWarrantyCatches;

    /**
     * @ORM\Column(type="boolean", default=false)
     */
    protected $forClosedSystem;

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
}
