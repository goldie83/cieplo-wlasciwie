<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="ratings")
 */
class Rating
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=2)
     */
    protected $rating;

    /**
     * @ORM\ManyToOne(targetEntity="Boiler", inversedBy="ratings")
     */
    protected $boiler;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Assert\DateTime
     */
    protected $createdAt;
}
