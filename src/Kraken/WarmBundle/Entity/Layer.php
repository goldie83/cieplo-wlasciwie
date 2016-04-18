<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="layer")
 */
class Layer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", nullable=false)
     * @Assert\Range(min="1", minMessage = "Min. grubość warstwy to 1cm", max="1000", maxMessage = "Masz ścianę powyżej 10m grubości? To już bunkier!")
     */
    protected $size;

    /**
     * @ORM\ManyToOne(targetEntity="Material", inversedBy="layers")
     * @ORM\JoinColumn(name="material_id", referencedColumnName="id", nullable=false)
     */
    protected $material;

    /**
     * @Assert\Callback
     */
    public function isLayerValid(ExecutionContextInterface $context)
    {
        if ($this->material && !$this->size) {
            $context->buildViolation('Podaj grubość warstwy')
                ->atPath('size')
                ->addViolation();
        }

        if (!$this->material && $this->size) {
            $context->buildViolation('Wybierz materiał')
                ->atPath('material')
                ->addViolation();
        }
    }

    public function __toString()
    {
        return (string) $this->getSize();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setMaterial(Material $material = null)
    {
        $this->material = $material;

        return $this;
    }

    public function getMaterial()
    {
        return $this->material;
    }
}
