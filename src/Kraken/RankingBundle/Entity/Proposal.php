<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="proposals")
 */
class Proposal
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $positive = true;

    /**
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->boilerProperties = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set positive
     *
     * @param boolean $positive
     * @return Property
     */
    public function setPositive($positive)
    {
        $this->positive = $positive;

        return $this;
    }

    /**
     * Get positive
     *
     * @return boolean
     */
    public function getPositive()
    {
        return $this->positive;
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
}
