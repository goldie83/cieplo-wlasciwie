<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="property_values")
 */
class PropertyValue
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
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="propertyValues")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", nullable=false)
     */
    protected $property;

    public function __construct()
    {
        $this->boilerProperties = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->property->getLabel() . ': '.$this->name;
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
     * @return PropertyValue
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
     * Set value
     *
     * @param integer $value
     * @return PropertyValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set property
     *
     * @param \Kraken\RankingBundle\Entity\Property $property
     * @return PropertyValue
     */
    public function setProperty(\Kraken\RankingBundle\Entity\Property $property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \Kraken\RankingBundle\Entity\Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Add boilerProperties
     *
     * @param \Kraken\RankingBundle\Entity\BoilerProperty $boilerProperties
     * @return PropertyValue
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
     * Set type
     *
     * @param string $type
     * @return PropertyValue
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getKind()
    {
        if ($this->value == 0) {
            return 'primary';
        }

        return $this->value < 0 ? 'danger' : 'success';
    }
}
