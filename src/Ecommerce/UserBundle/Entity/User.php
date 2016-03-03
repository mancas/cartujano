<?php

namespace Ecommerce\UserBundle\Entity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Ecommerce\LocationBundle\Entity\Address;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ecommerce\UserBundle\Entity\UserRepository")
 * @DoctrineAssert\UniqueEntity("email")
 * @UniqueEntity("email")
 */
class User implements UserInterface, \Serializable, EquatableInterface
{
    const AUTH_SALT = "Hyk3T1K0FWjo";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected  $id;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     * @Assert\Email();
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $lastName;

    /**
     * @ORM\OneToMany(targetEntity="Ecommerce\LocationBundle\Entity\Address", mappedBy="user")
     */
    protected $addresses;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $salt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updatedDate;

    /**
     * @var date $registeredDate
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="registeredDate", type="datetime", nullable=true)
     * @Assert\Date()
     */
    protected $registeredDate;

    /**
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    protected $deletedDate;

    /**
     * @ORM\Column(name="validated", type="boolean", nullable=true, options={"default" = 0})
     */
    protected $validated = false;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank()
     */
    protected $validatedCode;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $nif;

    /**
     * @ORM\OneToMany(targetEntity="Ecommerce\OrderBundle\Entity\Order", mappedBy="customer")
     */
    protected $orders;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Regex("/[0-9]{9}/")
     */
    protected $phone;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
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
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setRegisteredDate($registeredDate)
    {
        $this->registeredDate = $registeredDate;
    }

    public function getRegisteredDate()
    {
        return $this->registeredDate;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $updatedDate
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }

    /**
     * @return mixed
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    public function serialize()
    {
        return serialize(array($this->id, $this->password, $this->email));
    }

    public function unserialize($serialized)
    {
        list($this->id, $this->password, $this->email) = unserialize($serialized);
    }

    public function __toString()
    {
        return $this->getEmail();
    }

    public function isEqualTo(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        return $this->getEmail() == $user->getEmail();
    }

    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getRoles()
    {
        if ($this->validated)
            return array('ROLE_USER');
        else
            return array('ROLE_USER_NOT_VALIDATED');
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
     * @param mixed $addresses
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }

    public function getAddresses()
    {
        $addresses = array();

        foreach ($this->addresses as $address) {
            if ($address->getDeleted() == null) {
                $addresses[] = $address;
            }
        }

        return $addresses;
    }

    public function addAddress(\Ecommerce\LocationBundle\Entity\Address $address)
    {
        if (!$this->addresses->contains($address))
            $this->addresses->add($address);
    }

    public function removeAddress(\Ecommerce\LocationBundle\Entity\Address $address)
    {
        if ($this->addresses->contains($address))
            $this->addresses->remove($address);
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function getMainAddress()
    {
        $mainAddress = false;

        foreach ($this->addresses as $address) {
            if ($address->getMain()) {
                $mainAddress = $address;
                break;
            }
        }

        return $mainAddress;
    }

    /**
     * @param mixed $validated
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;
    }

    /**
     * @return mixed
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * @param mixed $validatedCode
     */
    public function setValidatedCode($validatedCode)
    {
        $this->validatedCode = $validatedCode;
    }

    /**
     * @return mixed
     */
    public function getValidatedCode()
    {
        return $this->validatedCode;
    }

    /**
     * @param mixed $deletedDate
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;
    }

    /**
     * @return mixed
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * @param mixed $nif
     */
    public function setNif($nif)
    {
        $this->nif = $nif;
    }

    /**
     * @return mixed
     */
    public function getNif()
    {
        return $this->nif;
    }

    public function isProfileComplete()
    {
        return ($this->name && $this->lastName && $this->nif);
    }

    /**
     * @param mixed $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return mixed
     */
    public function getOrders()
    {
        return $this->orders;
    }

    public function addOrder(\Ecommerce\OrderBundle\Entity\Order $order)
    {
        if (!$this->orders->contains($order))
            $this->orders->add($order);
    }

    public function removeOrder(\Ecommerce\OrderBundle\Entity\Order $order)
    {
        if ($this->orders->contains($order))
            $this->orders->remove($order);
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    function checkNIF($nif)
    {
        $str = trim($nif);
        $str = str_replace("-","",$str);
        $str = str_ireplace(" ","",$str);
        $n = substr($str,0,strlen($str)-1);
        $n = intval($n);
        if (!is_int($n)) {
            return false;
        }
        $l = substr($str,-1);
        if (!is_string($l)) {
            return false;
        }
        $letter = substr ("TRWAGMYFPDXBNJZSQVHLCKE", $n%23, 1);
        if ( strtolower($l) == strtolower($letter)) {
            return $n.$l;
        }
        else {
            return false;
        }
    }

    public function getTotalSpend()
    {
        $total = 0.0;
        foreach ($this->orders as $order) {
            $total += $order->getTotalAmount();
        }

        return $total;
    }
}