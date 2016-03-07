<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * @ORM\Entity
 * @ORM\Table(name="apartment")
 * @Assert\Callback(methods={"isNeighbourhoodValid"})
 */
class Apartment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=2)
     * @Assert\NotBlank
     * @Assert\Range(min="0", minMessage = "Liczba ścian musi być co najmniej równa zeru", max="4", maxMessage = "Cztery ściany to maksimum.")
     */
    protected $number_external_walls;

    /**
     * @ORM\Column(type="integer", length=2)
     * @Assert\NotBlank
     * @Assert\Range(min="0", minMessage = "Liczba ścian musi być co najmniej równa zeru", max="4", maxMessage = "Cztery ściany to maksimum.")
     */
    protected $number_unheated_walls;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('heated_room', 'unheated_room', 'outdoor')", nullable=false)
     */
    protected $whats_over;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('ground', 'heated_room', 'unheated_room', 'outdoor')", nullable=false)
     */
    protected $whats_under;

    /**
     * @ORM\OneToMany(targetEntity="House", mappedBy="apartment", cascade={"all"})
     */
    protected $houses;

    public function isNeighbourhoodValid(ExecutionContext $context)
    {
        if ($this->number_external_walls + $this->number_unheated_walls > 4) {
            $context->addViolationAt('number_external_walls', 'Mieszkanie ma w sumie co najwyżej cztery strony', [], null);
        }
    }

    public static function create()
    {
        $a = new self();

        $a->setNumberExternalWalls(2);
        $a->setNumberUnheatedWalls(1);

        return $a;
    }

    public function __construct()
    {
        $this->houses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set number_external_walls.
     *
     * @param int $numberExternalWalls
     *
     * @return Apartment
     */
    public function setNumberExternalWalls($numberExternalWalls)
    {
        $this->number_external_walls = $numberExternalWalls;

        return $this;
    }

    /**
     * Get number_external_walls.
     *
     * @return int
     */
    public function getNumberExternalWalls()
    {
        return $this->number_external_walls;
    }

    /**
     * Add houses.
     *
     * @param \Kraken\WarmBundle\Entity\House $houses
     *
     * @return Apartment
     */
    public function addHouse(\Kraken\WarmBundle\Entity\House $houses)
    {
        $this->houses[] = $houses;

        return $this;
    }

    /**
     * Remove houses.
     *
     * @param \Kraken\WarmBundle\Entity\House $houses
     */
    public function removeHouse(\Kraken\WarmBundle\Entity\House $houses)
    {
        $this->houses->removeElement($houses);
    }

    /**
     * Get houses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHouses()
    {
        return $this->houses;
    }

    /**
     * Set number_unheated_walls.
     *
     * @param int $numberUnheatedWalls
     *
     * @return Apartment
     */
    public function setNumberUnheatedWalls($numberUnheatedWalls)
    {
        $this->number_unheated_walls = $numberUnheatedWalls;

        return $this;
    }

    /**
     * Get number_unheated_walls.
     *
     * @return int
     */
    public function getNumberUnheatedWalls()
    {
        return $this->number_unheated_walls;
    }

    /**
     * Set whats_over.
     *
     * @param string $whatsOver
     *
     * @return Apartment
     */
    public function setWhatsOver($whatsOver)
    {
        $this->whats_over = $whatsOver;

        return $this;
    }

    /**
     * Get whats_over.
     *
     * @return string
     */
    public function getWhatsOver()
    {
        return $this->whats_over;
    }

    /**
     * Set whats_under.
     *
     * @param string $whatsUnder
     *
     * @return Apartment
     */
    public function setWhatsUnder($whatsUnder)
    {
        $this->whats_under = $whatsUnder;

        return $this;
    }

    /**
     * Get whats_under.
     *
     * @return string
     */
    public function getWhatsUnder()
    {
        return $this->whats_under;
    }
}
