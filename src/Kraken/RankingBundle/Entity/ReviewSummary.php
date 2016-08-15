<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="review_summaries")
 */
class ReviewSummary
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $reviewsNumber;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1, nullable=true)
     */
    protected $rating;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1, name="quality_rating", nullable=true)
     */
    protected $qualityRating;

    /**
     * @ORM\Column(type="string", name="quality_comment", nullable=true)
     */
    protected $qualityComment;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1, name="warranty_rating", nullable=true)
     */
    protected $warrantyRating;

    /**
     * @ORM\Column(type="string", name="warranty_comment", nullable=true)
     */
    protected $warrantyComment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $warrantyReviewsNumber;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1, name="operation_rating", nullable=true)
     */
    protected $operationRating;

    /**
     * @ORM\Column(type="string", name="operation_comment", nullable=true)
     */
    protected $operationComment;

    public function getId()
    {
        return $this->id;
    }

    public function getReviewsNumber()
    {
        return $this->reviewsNumber;
    }

    public function setReviewsNumber($reviewsNumber)
    {
        $this->reviewsNumber = $reviewsNumber;

        return $this;
    }

    public function getWarrantyReviewsNumber()
    {
        return $this->warrantyReviewsNumber;
    }

    public function setWarrantyReviewsNumber($warrantyReviewsNumber)
    {
        $this->warrantyReviewsNumber = $warrantyReviewsNumber;

        return $this;
    }

    public function getQualityRating()
    {
        return $this->qualityRating;
    }

    public function getQualityRatingString()
    {
        $rating = $this->qualityRating;

        return ((double) $rating) - ((int) $rating) == 0 ? (int) $rating : number_format($rating, 1, ',');
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

    public function getWarrantyRatingString()
    {
        $rating = $this->warrantyRating;

        return ((double) $rating) - ((int) $rating) == 0 ? (int) $rating : number_format($rating, 1, ',', ' ');
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

    public function getOperationRatingString()
    {
        $rating = $this->operationRating;

        return ((double) $rating) - ((int) $rating) == 0 ? (int) $rating : number_format($rating, 1, ',', ' ');
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

    public function getRating()
    {
        return $this->rating;
    }

    public function getRatingString()
    {
        return ((double) $this->rating) - ((int) $this->rating) == 0 ? (int) $this->rating : number_format($this->rating, 1, ',', ' ');
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
}
