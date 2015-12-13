<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="properties")
 */
class Property
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
    protected $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\OneToMany(targetEntity="PropertyValue", mappedBy="property")
     */
    protected $propertyValues;


    public function __construct()
    {
    }

    public function __toString()
    {
        return $this->label;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Property
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Property
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
     * Add propertyValues
     *
     * @param \Kraken\RankingBundle\Entity\PropertyValue $propertyValues
     * @return Property
     */
    public function addPropertyValue(\Kraken\RankingBundle\Entity\PropertyValue $propertyValues)
    {
        $this->propertyValues[] = $propertyValues;

        return $this;
    }

    /**
     * Remove propertyValues
     *
     * @param \Kraken\RankingBundle\Entity\PropertyValue $propertyValues
     */
    public function removePropertyValue(\Kraken\RankingBundle\Entity\PropertyValue $propertyValues)
    {
        $this->propertyValues->removeElement($propertyValues);
    }

    /**
     * Get propertyValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropertyValues()
    {
        return $this->propertyValues;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Property
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
}
