<?php

namespace AppBundle\Entity;

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
}
