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
     * @ORM\OneToMany(targetEntity="BoilerProperty", mappedBy="property")
     */
    protected $boilerProperties;

    /**
     * @ORM\Column(type="integer")
     */
    protected $meaning;

    /**
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    public function __construct()
    {
        $this->boilerProperties = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add boilerProperties
     *
     * @param \Kraken\RankingBundle\Entity\BoilerProperty $boilerProperties
     * @return Property
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
     * Set meaning
     *
     * @param integer $meaning
     * @return Property
     */
    public function setMeaning($meaning)
    {
        $this->meaning = $meaning;

        return $this;
    }

    /**
     * Get meaning
     *
     * @return integer
     */
    public function getMeaning()
    {
        return $this->meaning;
    }
}
