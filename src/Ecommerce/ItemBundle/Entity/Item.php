<?php

namespace Ecommerce\ItemBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ecommerce\ItemBundle\Entity\ItemRepository")
 * @DoctrineAssert\UniqueEntity("id")
 * @UniqueEntity("id")
 */
class Item
{
    const OUT_OF_STOCK = 1;
    const LOW_STOCK = 10;
    const MEDIUM_STOCK = 50;
    const HIGH_STOCK = 100;
    const TAX_ES = 21;

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
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $reference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $description;

    /**
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $price;

    /**
     * @ORM\Column(name="weight", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $weight;

    /**
     * @ORM\ManyToOne(targetEntity="Ecommerce\CategoryBundle\Entity\Subcategory", inversedBy="items")
     */
    protected $subcategory;

    /**
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @ORM\OneToMany(targetEntity="Ecommerce\ImageBundle\Entity\ImageItem", mappedBy="item", cascade={"persist", "remove", "merge"})
     */
    protected $images;

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
     * @ORM\Column(type="integer")
     */
    protected $stock = 1;

    /**
     * @ORM\ManyToOne(targetEntity="Ecommerce\ItemBundle\Entity\Tax", inversedBy="items")
     */
    protected $tax;

    /**
     * @ORM\ManyToOne(targetEntity="Ecommerce\ItemBundle\Entity\ItemPackage", inversedBy="items")
     */
    protected $package;

    /**
     * @ORM\Column(type="isCommercialItem", type="boolean", nullable=true, options={"default" = 0})
     */
    protected $isCommercialItem = false;

    public function __construct()
    {
        $this->images = new ArrayCollection();
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
     * @param mixed $subcategory
     */
    public function setSubcategory($subcategory)
    {
        $this->subcategory = $subcategory;
    }

    /**
     * @return mixed
     */
    public function getSubcategory()
    {
        return $this->subcategory;
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
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $name
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
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        if (!isset($this->tax)) {
            return $this->price;
        }

        return round($this->price + $this->price * ($this->tax->getTaxes()/100), 2);
    }

    public function getPriceWithoutTaxes()
    {
        return $this->price;
    }

    public function getTaxApplied()
    {
        return round($this->price * ($this->tax->getTaxes()/100), 2);
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

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        $images = array();
        foreach ($this->images as $image) {
            if ($image->getDeletedDate() == null)
                $images[] = $image;
        }

        return $images;
    }

    public function getImageMain()
    {
        foreach ($this->images as $image) {
            if ($image->getMain()) {
                return $image;
            }
        }

        return false;
    }

    /**
     * @param mixed $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }

    public function getStockLevel()
    {
        $currentStock = $this->getStock();
        if ($currentStock == 0) {
            return self::OUT_OF_STOCK;
        } else {
            if ($currentStock >= self::HIGH_STOCK) {
                return self::HIGH_STOCK;
            } else {
                if ($currentStock > self::LOW_STOCK) {
                    return self::MEDIUM_STOCK;
                } else {
                    if ($currentStock <= self::LOW_STOCK) {
                        return self::LOW_STOCK;
                    }
                }
            }
        }
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return mixed
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param mixed $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    /**
     * @return mixed
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param mixed $package
     */
    public function setPackage($package)
    {
        $this->package = $package;
    }

    /**
     * @return mixed
     */
    public function isCommercialItem()
    {
        return $this->isCommercialItem;
    }

    /**
     * @param mixed $forSale
     */
    public function setIsCommercialItem($forSale)
    {
        $this->isCommercialItem = $forSale;
    }
}
