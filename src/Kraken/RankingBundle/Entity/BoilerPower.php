<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="boiler_powers")
 */
class BoilerPower
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Boiler", inversedBy="boilerPowers")
     */
    protected $boiler;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $power;

    /**
     * @ORM\ManyToOne(targetEntity="FuelType", inversedBy="boilerPowers")
     */
    protected $fuelType;
}
