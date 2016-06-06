<?php
namespace Ecommerce\CartBundle\Model;

class CartItem implements \Serializable
{
    protected $id;

    protected $quantity;

    protected $price;

    protected $weight;

    public function __construct($itemId, $quantity, $price, $weight)
    {
        $this->id = $itemId;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->weight = $weight;
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
        return ($this->price * $this->quantity);
    }

    /**
     * @return mixed
     */
    public function getSinglePrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight * $this->quantity;
    }

    /**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array($this->id, $this->quantity, $this->price, $this->weight));
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
        list($this->id, $this->quantity, $this->price, $this->weight) = unserialize($serialized);
    }
}