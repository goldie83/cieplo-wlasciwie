<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="properties")
 */
class Property
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="BoilerProperty", mappedBy="property")
     */
    protected $boilerProperties;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     * @Assert\Date()
     */
    protected $date;

    /**
     * @ORM\Column(type="boolean", default=true)
     */
    protected $positive;

    /**
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;
}
