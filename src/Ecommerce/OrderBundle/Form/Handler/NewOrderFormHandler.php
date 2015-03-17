<?php

namespace Ecommerce\OrderBundle\Form\Handler;

use Ecommerce\OrderBundle\Entity\Order;
use Ecommerce\OrderBundle\Entity\OrderItem;
use Ecommerce\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;

class NewOrderFormHandler
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function handle(User $user, $cart, Request $request)
    {
        if ($request->isMethod('POST')) {
            $payMethod = $request->request->get('payment_option');
            $address = $this->em->getRepository('LocationBundle:Address')->findOneById($request->request->get('delivery_address'));
            $shipmentOption = $this->em->getRepository('ItemBundle:Shipment')->findOneById($request->request->get('shipment_option'));

            if (!isset($payMethod) || !isset($address) || !isset($shipmentOption)) {
                return array('result' => false);
            }

            $order = new Order();
            $order->setAddress($address);
            $order->setShipment($shipmentOption);
            $shipmentOption->addOrder($order);

            $cartItems = $cart->getCartItems();

            foreach ($cartItems as $cartItem) {
                $item = $this->em->getRepository('ItemBundle:Item')->findOneBy(array('id' => $cartItem->getId()));
                $orderItem = new OrderItem();
                $orderItem->setItem($item);
                $orderItem->setOrder($order);
                $orderItem->setQuantity($cartItem->getQuantity());
                $orderItem->setPrice($item->getPrice());
                $order->addItem($orderItem);
                //$item->setStock($item->getStock() - $cartItem->getQuantity());
                $this->em->persist($orderItem);
                $this->em->persist($item);
            }

            $order->setCustomer($user);
            $order->setDate(new \DateTime('now'));
            $order->setStatus(Order::STATUS_IN_PROCESS);

            $this->em->persist($order);
            $this->em->flush();
            return array('result' => true, 'order' => $order->getId(), 'payment' => $payMethod);
        }

        return array('result' => false);
    }

}