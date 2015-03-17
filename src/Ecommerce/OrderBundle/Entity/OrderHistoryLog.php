<?php

namespace Ecommerce\OrderBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="OrderHistoryLog")
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity("id")
 * @UniqueEntity("id")
 */
class OrderHistoryLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ecommerce\OrderBundle\Entity\OrderHistory", inversedBy="logs")
     */
    protected $orderHistory;

    /**
     * @ORM\Column(name="log", type="string", length=100, nullable=true)
     */
    protected $log;

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
    public function getOrderHistory()
    {
        return $this->orderHistory;
    }

    /**
     * @param mixed $orderHistory
     */
    public function setOrderHistory($orderHistory)
    {
        $this->orderHistory = $orderHistory;
    }

    /**
     * @return mixed
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param mixed $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }
}