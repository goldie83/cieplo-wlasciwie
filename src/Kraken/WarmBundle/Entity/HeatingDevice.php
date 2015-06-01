<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="heating_device")
 */
class HeatingDevice
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $detail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $for_legacy_setup;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $for_advice;

    /**
     * @ORM\OneToMany(targetEntity="HeatingVariant", mappedBy="heatingDevice")
     */
    protected $heatingVariants;

    public function __toString()
    {
        return $this->name;
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
     * Set type
     *
     * @param string $type
     * @return HeatingDevice
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

    /**
     * Set name
     *
     * @param string $name
     * @return HeatingDevice
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
     * Set detail
     *
     * @param string $detail
     * @return HeatingDevice
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string 
     */
    public function getDetail()
    {
        return $this->detail;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->heatingVariants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add heatingVariants
     *
     * @param \Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants
     * @return HeatingDevice
     */
    public function addHeatingVariant(\Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants)
    {
        $this->heatingVariants[] = $heatingVariants;

        return $this;
    }

    /**
     * Remove heatingVariants
     *
     * @param \Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants
     */
    public function removeHeatingVariant(\Kraken\WarmBundle\Entity\HeatingVariant $heatingVariants)
    {
        $this->heatingVariants->removeElement($heatingVariants);
    }

    /**
     * Get heatingVariants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHeatingVariants()
    {
        return $this->heatingVariants;
    }

    /**
     * Set for_legacy_setup
     *
     * @param boolean $forLegacySetup
     * @return HeatingDevice
     */
    public function setForLegacySetup($forLegacySetup)
    {
        $this->for_legacy_setup = $forLegacySetup;

        return $this;
    }

    /**
     * Get for_legacy_setup
     *
     * @return boolean 
     */
    public function getForLegacySetup()
    {
        return $this->for_legacy_setup;
    }

    /**
     * Set for_advice
     *
     * @param boolean $forAdvice
     * @return HeatingDevice
     */
    public function setForAdvice($forAdvice)
    {
        $this->for_advice = $forAdvice;

        return $this;
    }

    /**
     * Get for_advice
     *
     * @return boolean 
     */
    public function getForAdvice()
    {
        return $this->for_advice;
    }
}