<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="wall")
 */
class Wall
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="House", inversedBy="walls")
     * @ORM\JoinColumn(name="house_id", referencedColumnName="id", nullable=false)
     */
    protected $house;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="construction_layer_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     * @Assert\Valid
     */
    protected $construction_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="outside_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $outside_layer;

    /**
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="extra_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $extra_isolation_layer;

    public function __construct()
    {
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set house.
     *
     * @param \Kraken\WarmBundle\Entity\House $house
     *
     * @return Wall
     */
    public function setHouse(\Kraken\WarmBundle\Entity\House $house = null)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Get house.
     *
     * @return \Kraken\WarmBundle\Entity\House
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set construction_layer.
     *
     * @param \Kraken\WarmBundle\Entity\Layer $constructionLayer
     *
     * @return Wall
     */
    public function setConstructionLayer(\Kraken\WarmBundle\Entity\Layer $constructionLayer = null)
    {
        $this->construction_layer = $constructionLayer;

        return $this;
    }

    /**
     * Get construction_layer.
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getConstructionLayer()
    {
        return $this->construction_layer;
    }

    /**
     * Set isolation_layer.
     *
     * @param \Kraken\WarmBundle\Entity\Layer $isolationLayer
     *
     * @return Wall
     */
    public function setIsolationLayer(\Kraken\WarmBundle\Entity\Layer $isolationLayer = null)
    {
        $this->isolation_layer = $isolationLayer;

        return $this;
    }

    /**
     * Get isolation_layer.
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getIsolationLayer()
    {
        return $this->isolation_layer;
    }

    /**
     * Set outside_layer.
     *
     * @param \Kraken\WarmBundle\Entity\Layer $outsideLayer
     *
     * @return Wall
     */
    public function setOutsideLayer(\Kraken\WarmBundle\Entity\Layer $outsideLayer = null)
    {
        $this->outside_layer = $outsideLayer;

        return $this;
    }

    /**
     * Get outside_layer.
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getOutsideLayer()
    {
        return $this->outside_layer;
    }

    /**
     * Set extra_isolation_layer.
     *
     * @param \Kraken\WarmBundle\Entity\Layer $extraIsolationLayer
     *
     * @return Wall
     */
    public function setExtraIsolationLayer(\Kraken\WarmBundle\Entity\Layer $extraIsolationLayer = null)
    {
        $this->extra_isolation_layer = $extraIsolationLayer;

        return $this;
    }

    /**
     * Get extra_isolation_layer.
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getExtraIsolationLayer()
    {
        return $this->extra_isolation_layer;
    }

    public function getLayers()
    {
        $layers = array(
            $this->getConstructionLayer(),
            $this->getIsolationLayer(),
            $this->getOutsideLayer(),
            $this->getExtraIsolationLayer(),
        );

        foreach ($layers as $k => $l) {
            if (!$l) {
                unset($layers[$k]);
            }
        }

        return $layers;
    }
}
