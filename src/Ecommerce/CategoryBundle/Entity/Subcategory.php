<?php

namespace Ecommerce\CategoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Ecommerce\ItemBundle\Entity\Item;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ecommerce\CategoryBundle\Entity\SubcategoryRepository")
 * @DoctrineAssert\UniqueEntity("id")
 * @UniqueEntity("id")
 */
class Subcategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @ORM\OneToMany(targetEntity="Ecommerce\ItemBundle\Entity\Item", mappedBy="subcategory")
     */
    protected $items;

    /**
     * @ORM\ManyToOne(targetEntity="Ecommerce\CategoryBundle\Entity\Category", inversedBy="subcategories")
     */
    protected $category;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="deleted", type="date", nullable=true)
     * @Assert\Date()
     */
    protected $deleted;

    /**
     * @ORM\Column(name="useInIndex", type="boolean", nullable=true, options={"default" = 0})
     */
    protected $useInIndex = false;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $category
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    public function addItem($item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }
    }

    public function removeItem($item)
    {
        if ($this->items->contains($item)) {
            $this->items->remove($item);
        }
    }

    public function hasNonCommercialItems()
    {
        foreach ($this->items as $item) {
            if (!$item->isCommercialItem()) {
                return true;
            }
        }

        return false;
    }

    public function getNonCommercialItems()
    {
        $result = array();
        foreach ($this->items as $item) {
            if (!$item->isCommercialitem()) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param mixed $useInIndex
     */
    public function setUseInIndex($useInIndex)
    {
        $this->useInIndex = $useInIndex;
    }

    /**
     * @return mixed
     */
    public function getUseInIndex()
    {
        return $this->useInIndex;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }


}