<?php

namespace Ecommerce\CartBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ecommerce\FrontendBundle\Util\StringHelper;

class Cart implements \Serializable
{
    protected $identifier;

    protected $expiredAt;

    protected $cartItems;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
        $this->identifier = StringHelper::getUniqueIdentifier();
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $expiredAt
     */
    public function setExpiredAt($expiredAt)
    {
        $this->expiredAt = $expiredAt;
    }

    /**
     * @return mixed
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * @param mixed $cartItems
     */
    public function setCartItems($cartItems)
    {
        $this->cartItems = $cartItems;
    }

    /**
     * @return mixed
     */
    public function getCartItems()
    {
        return $this->cartItems;
    }

    public function addCartItem(\Ecommerce\CartBundle\Model\CartItem $cartItem)
    {
        if ($this->contains($cartItem)) {
            $this->incrementQuantity($cartItem->getId(), $cartItem->getQuantity());
        } else {
            $this->cartItems->add($cartItem);
        }
    }

    public function removeCartItem(\Ecommerce\CartBundle\Model\CartItem $cartItem)
    {
        if ($this->contains($cartItem)) {
            $this->cartItems->removeElement($cartItem);
        }
    }

    public function getCartItemById($id)
    {
        foreach ($this->cartItems as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        return false;
    }

    public function contains(\Ecommerce\CartBundle\Model\CartItem $cartItem)
    {
        foreach ($this->cartItems as $item) {
            if ($item->getId() == $cartItem->getId()) {
                return true;
            }
        }

        return false;
    }

    public function incrementQuantity($id, $quantity)
    {
        $item = $this->getCartItemById($id);
        $item->setQuantity($item->getQuantity() + $quantity);
    }

    public function getCartItem(\Ecommerce\CartBundle\Model\CartItem $cartItem)
    {
        foreach ($this->cartItems as $item) {
            if ($item->getId() == $cartItem->getId()) {
                return $item;
            }
        }

        return false;
    }

    public function getCartTotal()
    {
        $total = 0.0;
        foreach ($this->cartItems as $item) {
            $total += $item->getPrice();
        }

        return $total;
    }

    public function getCartWeight()
    {
        $weight = 0.0;
        foreach ($this->cartItems as $item) {
            $weight += $item->getWeight();
        }

        // Grams
        return $weight/1000;
    }

    public function getCartItemsCount()
    {
        $count = 0;
        foreach ($this->cartItems as $item) {
            $count += $item->getQuantity();
        }

        return $count;
    }

    public function resetCart()
    {
        $this->cartItems = new ArrayCollection();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array($this->expiredAt, $this->cartItems));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        list($this->expiredAt, $this->cartItems) = unserialize($serialized);
    }
}