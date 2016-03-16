<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="singular_name")
     */
    protected $singularName;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sort = 0;

    /**
     * @ORM\OneToMany(targetEntity="Boiler", mappedBy="category")
     */
    protected $boilers;

    /**
     * @ORM\OneToMany(targetEntity="Search", mappedBy="category")
     */
    protected $searches;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->boilers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     * Set name.
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set sort.
     *
     * @param int $sort
     *
     * @return Category
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     *
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Add boilers.
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $boilers
     *
     * @return Category
     */
    public function addBoiler(\Kraken\RankingBundle\Entity\Boiler $boilers)
    {
        $this->boilers[] = $boilers;

        return $this;
    }

    /**
     * Remove boilers.
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $boilers
     */
    public function removeBoiler(\Kraken\RankingBundle\Entity\Boiler $boilers)
    {
        $this->boilers->removeElement($boilers);
    }

    /**
     * Get boilers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoilers()
    {
        return $this->boilers;
    }

    /**
     * Set singularName.
     *
     * @param string $singularName
     *
     * @return Category
     */
    public function setSingularName($singularName)
    {
        $this->singularName = $singularName;

        return $this;
    }

    /**
     * Get singularName.
     *
     * @return string
     */
    public function getSingularName()
    {
        return $this->singularName;
    }

    /**
     * Add searches.
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $searches
     *
     * @return Category
     */
    public function addSearch(\Kraken\RankingBundle\Entity\Boiler $searches)
    {
        $this->searches[] = $searches;

        return $this;
    }

    /**
     * Remove searches.
     *
     * @param \Kraken\RankingBundle\Entity\Boiler $searches
     */
    public function removeSearch(\Kraken\RankingBundle\Entity\Boiler $searches)
    {
        $this->searches->removeElement($searches);
    }

    /**
     * Get searches.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSearches()
    {
        return $this->searches;
    }

    /**
     * Set parent.
     *
     * @param \Kraken\RankingBundle\Entity\Category $parent
     *
     * @return Category
     */
    public function setParent(\Kraken\RankingBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return \Kraken\RankingBundle\Entity\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children.
     *
     * @param \Kraken\RankingBundle\Entity\Category $children
     *
     * @return Category
     */
    public function addChild(\Kraken\RankingBundle\Entity\Category $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children.
     *
     * @param \Kraken\RankingBundle\Entity\Category $children
     */
    public function removeChild(\Kraken\RankingBundle\Entity\Category $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getChildrenIds()
    {
        $ids = [];

        foreach ($this->children as $c) {
            $ids[] = $c->getId();
        }

        return $ids;
    }

    public function getIndentedName()
    {
        return count($this->children) == 0 ? '-> '.$this->name : $this->name;
    }
}
