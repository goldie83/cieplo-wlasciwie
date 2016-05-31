<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="notices")
 */
class Notice
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Boiler", inversedBy="notices")
     * @ORM\JoinColumn(name="boiler_id", referencedColumnName="id")
     */
    protected $boiler;

    /**
     * @ORM\ManyToOne(targetEntity="NoticePrototype", inversedBy="notices", cascade={"all"})
     * @ORM\JoinColumn(name="prototype_id", referencedColumnName="id", nullable=false)
     */
    protected $noticePrototype;

    /**
     * @ORM\Column(type="integer")
     */
    protected $importance = 0;

    /**
     * @ORM\Column(type="string")
     */
    protected $valuation;

    /**
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;


    public function __toString()
    {
        return $this->noticePrototype->getLabel();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setBoiler(Boiler $boiler)
    {
        $this->boiler = $boiler;

        return $this;
    }

    public function getBoiler()
    {
        return $this->boiler;
    }

    public function setNoticePrototype($prototype)
    {
        $this->noticePrototype = $prototype;

        return $this;
    }

    public function getNoticePrototype()
    {
        return $this->noticePrototype;
    }

    public function setImportance($importance)
    {
        $this->importance = $importance;

        return $this;
    }

    public function getImportance()
    {
        return $this->importance;
    }

    public function setValuation($valuation)
    {
        $this->valuation = $valuation;

        return $this;
    }

    public function getValuation()
    {
        return $this->valuation;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }
}
