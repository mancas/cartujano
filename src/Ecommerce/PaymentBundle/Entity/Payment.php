<?php
namespace Ecommerce\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Payment
 * @ORM\Entity(repositoryClass="Ecommerce\PaymentBundle\Entity\PaymentRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"transfer"="Ecommerce\PaymentBundle\Entity\Transfer", "paypal"="Ecommerce\PayPalBundle\Entity\PaypalPayment"})
 */
abstract class Payment
{
    const PAYMENT_TYPE_PAYPAL = "Paypal";
    const PAYMENT_TYPE_TRANSFERENCIA = "Trasferencia";
    const CC = "";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Ecommerce\PaymentBundle\Entity\Bill", mappedBy="payment");
     */
    protected $bill;

    /**
     * @ORM\OneToOne(targetEntity="Ecommerce\OrderBundle\Entity\Order", mappedBy="payment", cascade={"persist"})
     */
    protected $order;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="date")
     */
    protected $created;

    /**
     * @ORM\Column(type="float")
     */
    protected $total;

    /**
     * @param mixed $bill
     */
    public function setBill(\Ecommerce\PaymentBundle\Entity\Bill $bill)
    {
        $this->bill = $bill;
    }

    /**
     * @return mixed
     */
    public function getBill()
    {
        return $this->bill;
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
     * @param mixed $order
     */
    public function setOrder(\Ecommerce\OrderBundle\Entity\Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    public function getType()
    {
        return get_class($this);
    }
}