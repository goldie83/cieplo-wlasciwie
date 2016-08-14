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
     * @Assert\NotBlank()
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
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $practice;

    /**
     * @ORM\Column(type="integer", name="boiler_practice", nullable=true)
     */
    protected $boilerPractice;

    /**
     * @ORM\Column(type="integer", name="quality_rating", nullable=true)
     */
    protected $qualityRating;

    /**
     * @ORM\Column(type="text", name="quality_comment", nullable=true)
     */
    protected $qualityComment;

    /**
     * @ORM\Column(type="integer", name="warranty_rating", nullable=true)
     */
    protected $warrantyRating;

    /**
     * @ORM\Column(type="text", name="warranty_comment", nullable=true)
     */
    protected $warrantyComment;

    /**
     * @ORM\Column(type="integer", name="operation_rating", nullable=true)
     */
    protected $operationRating;

    /**
     * @ORM\Column(type="text", name="operation_comment", nullable=true)
     */
    protected $operationComment;

    /**
     * @ORM\ManyToOne(targetEntity="BoilerPower")
     * @ORM\JoinColumn(name="boiler_power_id", referencedColumnName="id", nullable=true)
     */
    protected $boilerPower;

    /**
     * @ORM\Column(type="integer", name="house_area", nullable=true)
     */
    protected $houseArea;

    /**
     * @ORM\Column(type="integer", name="house_standard", nullable=true)
     */
    protected $houseStandard;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $rating;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Email(
     *    message = "To nie jest prawidłowy adres e-mail"
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Ip
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

    /**
     * @ORM\Column(type="boolean", name="is_accepted")
     */
    protected $accepted = false;

    /**
     * @ORM\Column(type="boolean", name="is_revoked")
     */
    protected $revoked = false;


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

    public function getPractice()
    {
        return $this->practice;
    }

    public function setPractice($practice)
    {
        $this->practice = $practice;

        return $this;
    }

    public function getBoilerPractice()
    {
        return $this->boilerPractice;
    }

    public function setBoilerPractice($boilerPractice)
    {
        $this->boilerPractice = $boilerPractice;

        return $this;
    }

    public function getQualityRating()
    {
        return $this->qualityRating;
    }

    public function setQualityRating($qualityRating)
    {
        $this->qualityRating = $qualityRating;

        return $this;
    }

    public function getQualityComment()
    {
        return $this->qualityComment;
    }

    public function setQualityComment($qualityComment)
    {
        $this->qualityComment = $qualityComment;

        return $this;
    }

    public function getWarrantyRating()
    {
        return $this->warrantyRating;
    }

    public function setWarrantyRating($warrantyRating)
    {
        $this->warrantyRating = $warrantyRating;

        return $this;
    }

    public function getWarrantyComment()
    {
        return $this->warrantyComment;
    }

    public function setWarrantyComment($warrantyComment)
    {
        $this->warrantyComment = $warrantyComment;

        return $this;
    }

    public function getOperationRating()
    {
        return $this->operationRating;
    }

    public function setOperationRating($operationRating)
    {
        $this->operationRating = $operationRating;

        return $this;
    }

    public function getOperationComment()
    {
        return $this->operationComment;
    }

    public function setOperationComment($operationComment)
    {
        $this->operationComment = $operationComment;

        return $this;
    }

    public function getBoilerPower()
    {
        return $this->boilerPower;
    }

    public function setBoilerPower($boilerPower)
    {
        $this->boilerPower = $boilerPower;

        return $this;
    }

    public function getHouseArea()
    {
        return $this->houseArea;
    }

    public function setHouseArea($houseArea)
    {
        $this->houseArea = $houseArea;

        return $this;
    }

    public function getHouseStandard()
    {
        return $this->houseStandard;
    }

    public function setHouseStandard($houseStandard)
    {
        $this->houseStandard = $houseStandard;

        return $this;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
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

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

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

    public function isAccepted()
    {
        return $this->accepted;
    }

    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function isRevoked()
    {
        return $this->revoked;
    }

    public function setRevoked($revoked)
    {
        $this->revoked = $revoked;

        return $this;
    }
}
