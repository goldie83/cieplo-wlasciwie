<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="boiler_properties")
 */
class BoilerProperty
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Boiler", inversedBy="boilerProperties")
     * @ORM\JoinColumn(name="boiler_id", referencedColumnName="id", nullable=false)
     */
    protected $boiler;

    /**
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="boilerProperties")
     */
    protected $property;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->label == '' ? $this->property->getLabel() : $this->label;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return BoilerProperty
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
     * @return BoilerProperty
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
     * Set boiler
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $boiler
     * @return BoilerProperty
     */
    public function setBoiler(\Kraken\RankingBundle\Entity\Boiler $boiler = null)
    {
        $this->boiler = $boiler;

        return $this;
    }

    /**
     * Get boiler
     *
     * @return \Kraken\RankingBundle\Entity\Boiler
     */
    public function getBoiler()
    {
        return $this->boiler;
    }

    /**
     * Set property
     *
     * @param \Kraken\RankingBundle\Entity\Property $property
     * @return BoilerProperty
     */
    public function setProperty(\Kraken\RankingBundle\Entity\Property $property = null)
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
}
