<?php

namespace Ecommerce\OrderBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="OrderHistory")
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity("id")
 * @UniqueEntity("id")
 */
class OrderHistory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Ecommerce\OrderBundle\Entity\Order", mappedBy="orderHistory", cascade={"persist"})
     */
    protected $order;

    /**
     * @ORM\OneToMany(targetEntity="Ecommerce\OrderBundle\Entity\OrderHistoryLog", mappedBy="orderHistory", cascade={"persist"})
     */
    protected $logs;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param mixed $logs
     */
    public function setLogs($logs)
    {
        $this->logs = $logs;
    }

    public function addLog(OrderHistoryLog $log)
    {
        $this->logs->add($log);
    }
}