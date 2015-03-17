<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\BackendBundle\Form\Type\OrderSearchType;
use Ecommerce\OrderBundle\Entity\Order;
use Ecommerce\OrderBundle\Entity\OrderHistoryLog;
use Ecommerce\OrderBundle\Event\OrderEvent;
use Ecommerce\OrderBundle\Event\OrderEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends CustomController
{
    public function listAction(Request $request)
    {
        $em = $this->getEntityManager();
        $form = $this->createForm(new OrderSearchType());
        $form->submit($request);
        $criteria = $this->getCriteriaFromSearchForm($form);
        $orders = $em->getRepository("OrderBundle:Order")->findBySearchCriteria($criteria);

        return $this->render('BackendBundle:Order:list.html.twig', array('orders' => $orders, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("order", class="OrderBundle:Order")
     */
    public function viewAction(Order $order)
    {
        return $this->render('BackendBundle:Order:view.html.twig', array('order' => $order));
    }


    public function deleteItemAction($itemId, $orderId)
    {
        $em = $this->getEntityManager();
        $orderItem = $em->getRepository('OrderBundle:OrderItem')->findOneBy(array('order_id' => $orderId, 'item_id' => $itemId));

        if ($orderItem) {
            $em->remove($orderItem);
            $em->flush();

            $this->setTranslatedFlashMessage('Se ha eliminado el producto del pedido');
        }

        return $this->redirect($this->generateUrl('admin_order_view', array('id' => $orderId)));
    }

    /**
     * @ParamConverter("order", class="OrderBundle:Order")
     */
    public function markAsSentAction(Order $order)
    {
        $em = $this->getEntityManager();

        $order->setStatus(Order::STATUS_SEND);
        $orderHistoryLog = new OrderHistoryLog();
        $orderHistoryLog->setLog(Order::STATUS_SEND);
        $order->getOrderHistory()->addLog($orderHistoryLog);
        $orderHistoryLog->setOrderHistory($order->getOrderHistory());
        $em->persist($orderHistoryLog);
        $em->persist($order);
        $em->flush();

        $orderEvent = new OrderEvent($order);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(OrderEvents::STATUS_CHANGED, $orderEvent);

        $this->setTranslatedFlashMessage('El pedido ha sido marcado como enviado y se ha notificado al comprador');

        return $this->redirect($this->generateUrl('admin_order_index'));
    }

    /**
     * @ParamConverter("order", class="OrderBundle:Order")
     */
    public function markAsReadyToTakeAction(Order $order)
    {
        $em = $this->getEntityManager();

        $order->setStatus(Order::STATUS_READY_TO_TAKE);
        $orderHistoryLog = new OrderHistoryLog();
        $orderHistoryLog->setLog(Order::STATUS_READY_TO_TAKE);
        $order->getOrderHistory()->addLog($orderHistoryLog);
        $orderHistoryLog->setOrderHistory($order->getOrderHistory());
        $em->persist($orderHistoryLog);
        $em->persist($order);
        $em->flush();

        $orderEvent = new OrderEvent($order);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(OrderEvents::STATUS_CHANGED, $orderEvent);

        $this->setTranslatedFlashMessage('El pedido ha sido marcado como listo para recoger en tienda y se ha notificado al comprador');

        return $this->redirect($this->generateUrl('admin_order_index'));
    }

    /**
     * @ParamConverter("order", class="OrderBundle:Order")
     */
    public function markAsOutOfStockAction(Order $order)
    {
        $em = $this->getEntityManager();

        $order->setStatus(Order::STATUS_OUT_OF_STOCK);
        $orderHistoryLog = new OrderHistoryLog();
        $orderHistoryLog->setLog(Order::STATUS_OUT_OF_STOCK);
        $order->getOrderHistory()->addLog($orderHistoryLog);
        $orderHistoryLog->setOrderHistory($order->getOrderHistory());
        $em->persist($orderHistoryLog);
        $em->persist($order);
        $em->flush();

        $orderEvent = new OrderEvent($order);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(OrderEvents::STATUS_CHANGED, $orderEvent);

        $this->setTranslatedFlashMessage('El pedido ha sido marcado como rotura de stock y se ha notificado al comprador');

        return $this->redirect($this->generateUrl('admin_order_index'));
    }

    /**
     * @ParamConverter("order", class="OrderBundle:Order")
     */
    public function markAsCancelAction(Order $order)
    {
        $em = $this->getEntityManager();
        $order->setStatus(Order::STATUS_CANCELED);
        $orderHistoryLog = new OrderHistoryLog();
        $orderHistoryLog->setLog(Order::STATUS_CANCELED);
        $order->getOrderHistory()->addLog($orderHistoryLog);
        $orderHistoryLog->setOrderHistory($order->getOrderHistory());
        $em->persist($orderHistoryLog);
        $em->persist($order);
        $em->flush();

        $orderEvent = new OrderEvent($order);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(OrderEvents::STATUS_CHANGED, $orderEvent);

        $this->setTranslatedFlashMessage('El pedido ha sido cancelado, recuerda que debes devolver el dinero al usuario en el caso de que haya realizado el pago.');

        return $this->redirect($this->generateUrl('admin_order_index'));
    }
}