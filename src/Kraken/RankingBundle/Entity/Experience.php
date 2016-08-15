<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kraken\RankingBundle\Repository\ExperienceRepository")
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
     * @ORM\ManyToOne(targetEntity="Review", inversedBy="ownExperiences")
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
     * @ORM\Column(type="boolean", name="is_accepted")
     */
    protected $accepted = false;

    /**
     * @ORM\OneToMany(targetEntity="ReviewExperience", mappedBy="experience", cascade={"all"}, orphanRemoval=true)
     */
    protected $reviewExperiences;

    public function __toString()
    {
        $boilerName = $this->getBoiler() ? $this->getBoiler()->getName() : '[?]';

        return $boilerName.': '.$this->getTitle();
    }

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

    public function isAccepted()
    {
        return $this->accepted;
    }

    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getReviewExperiences()
    {
        return $this->reviewExperiences;
    }

    public function addReviewExperience(ReviewExperience $reviewExperience)
    {
        $this->reviewExperiences[] = $reviewExperience;

        return $this;
    }

    public function removeReviewExperience(ReviewExperience $reviewExperience)
    {
        $this->reviewExperiences->removeElement($reviewExperience);
    }

    public function countConfirmations()
    {
        $count = 0;

        foreach ($this->reviewExperiences as $re) {
            if ($re->isConfirmed() == true) {
                ++$count;
            }
        }

        return $count;
    }

    public function countNegations()
    {
        return count($this->reviewExperiences) - $this->countConfirmations();
    }
}
