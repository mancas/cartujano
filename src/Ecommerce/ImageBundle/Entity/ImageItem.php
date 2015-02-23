<?php

namespace Ecommerce\ImageBundle\Entity;

use Ecommerce\ImageBundle\Entity\Image;
use Ecommerce\ImageBundle\Util\FileHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class ImageItem extends Image
{
    CONST MAX_WIDTH = 1024;
    CONST MAX_HEIGHT = 768;
    protected $subdirectory = "images/products";
    protected $maxWidth = self::MAX_WIDTH;
    protected $maxHeight = self::MAX_HEIGHT;

    /**
     * @ORM\ManyToOne(targetEntity="Ecommerce\ItemBundle\Entity\Item", inversedBy="images")
     */
    protected $item;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" = 0})
     */
    protected $main = false;

    public function setItem(\Ecommerce\ItemBundle\Entity\Item $item)
    {
        $this->item = $item;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function createImageItemCart()
    {
        $thumb = $this->getImageItemCart();
        if (!$thumb) {
            $thumb = new ImageItemCart();
        }

        return $thumb;
    }

    public function createCopies()
    {
        list($oldRoute, $copies) = parent::createCopies();

        if ($cart = $this->createImageItemCart()) {
            $copies[] = $cart;
        }

        return array($oldRoute, $copies);
    }

    public function getMain()
    {
        return $this->main;
    }

    public function setMain($main)
    {
        $this->main = $main;
    }

}
