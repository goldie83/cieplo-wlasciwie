<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="review_experiences")
 */
class ReviewExperience
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Experience", inversedBy="reviewExperiences")
     */
    protected $experience;

    /**
     * @ORM\ManyToOne(targetEntity="Review", inversedBy="reviewExperiences")
     */
    protected $review;

    /**
     * @ORM\Column(type="boolean", name="is_own")
     */
    protected $own = false;

    /**
     * @ORM\Column(type="boolean", name="confirmed")
     */
    protected $confirmed = false;

    public function getReview()
    {
        return $this->review;
    }

    public function setReview(Review $review)
    {
        $this->review = $review;

        return $this;
    }

    public function getExperience()
    {
        return $this->experience;
    }

    public function setExperience(Experience $experience)
    {
        $this->experience = $experience;

        return $this;
    }

    public function isConfirmed()
    {
        return $this->confirmed;
    }

    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }
}
