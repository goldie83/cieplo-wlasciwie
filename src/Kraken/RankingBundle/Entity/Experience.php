<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="experiences")
 */
class Experience
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Boiler", inversedBy="experiences")
     * @ORM\JoinColumn(name="boiler_id", referencedColumnName="id", nullable=false)
     */
    protected $boiler;

    /**
     * @ORM\ManyToOne(targetEntity="Review", inversedBy="experiences")
     * @ORM\JoinColumn(name="parent_review_id", referencedColumnName="id", nullable=true)
     */
    protected $parentReview;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\OneToMany(targetEntity="ReviewExperience", mappedBy="experience", cascade={"all"}, orphanRemoval=true)
     */
    protected $reviewExperiences;

    public function getId()
    {
        return $this->id;
    }

    public function getBoiler()
    {
        return $this->boiler;
    }

    public function setBoiler(Boiler $boiler)
    {
        $this->boiler = $boiler;

        return $this;
    }

    public function getParentReview()
    {
        return $this->parentReview;
    }

    public function setParentReview(Review $parentReview)
    {
        $this->parentReview = $parentReview;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

}
