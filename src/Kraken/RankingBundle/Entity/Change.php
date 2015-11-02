<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="changes")
 */
class Change
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Boiler", inversedBy="changes")
     */
    protected $boiler;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     * @Assert\Date()
     */
    protected $date;

    /**
     * @ORM\Column(type="string", length=2)
     */
    protected $oldRating;

    /**
     * @ORM\Column(type="string", length=2)
     */
    protected $newRating;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

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
     * Set date
     *
     * @param \DateTime $date
     * @return Change
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set oldRating
     *
     * @param string $oldRating
     * @return Change
     */
    public function setOldRating($oldRating)
    {
        $this->oldRating = $oldRating;

        return $this;
    }

    /**
     * Get oldRating
     *
     * @return string 
     */
    public function getOldRating()
    {
        return $this->oldRating;
    }

    /**
     * Set newRating
     *
     * @param string $newRating
     * @return Change
     */
    public function setNewRating($newRating)
    {
        $this->newRating = $newRating;

        return $this;
    }

    /**
     * Get newRating
     *
     * @return string 
     */
    public function getNewRating()
    {
        return $this->newRating;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Change
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
     * @return Change
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
}
