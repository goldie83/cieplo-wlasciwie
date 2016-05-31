<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Kraken\RankingBundle\Repository\ReviewRepository")
 * @ORM\Table(name="reviews")
 * @UniqueEntity(fields={"boiler", "email"}, message="Z tego adresu została już dodana opinia o tym kotle.", errorPath="email")
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Boiler", inversedBy="reviews")
     * @ORM\JoinColumn(name="boiler_id", referencedColumnName="id", nullable=false)
     */
    protected $boiler;

    /**
     * @ORM\OneToMany(targetEntity="ReviewExperience", mappedBy="review", cascade={"all"}, orphanRemoval=true)
     */
    protected $reviewExperiences;

    /**
     * @ORM\OneToMany(targetEntity="Experience", mappedBy="parentReview", cascade={"all"}, orphanRemoval=true)
     */
    protected $ownExperiences;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email(
     *    message = "To nie jest prawidłowy adres e-mail"
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ip;

    /**
     * @ORM\Column(type="string", nullable=true, name="user_agent")
     */
    protected $userAgent;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    public function __construct()
    {
        $this->reviewExperiences = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ownExperiences = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setBoiler(\Kraken\RankingBundle\Entity\Boiler $boiler = null)
    {
        $this->boiler = $boiler;

        return $this;
    }

    public function getBoiler()
    {
        return $this->boiler;
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

    public function getOwnExperiences()
    {
        return $this->ownExperiences;
    }

    public function addOwnExperience(Experience $ownExperience)
    {
        $this->ownExperiences[] = $ownExperience;

        return $this;
    }

    public function removeOwnExperience(Experience $ownExperience)
    {
        $this->ownExperiences->removeElement($ownExperience);
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
}
