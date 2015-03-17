<?php

namespace Ecommerce\PaymentBundle\Controller;

use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\OrderBundle\Entity\Order;
use Ecommerce\OrderBundle\Event\OrderEvent;
use Ecommerce\OrderBundle\Event\OrderEvents;
use Ecommerce\PaymentBundle\Entity\Bill;
use Ecommerce\PaymentBundle\Entity\Transfer;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PaymentController extends CustomController
{
    /**
     * @ParamConverter("order", class="OrderBundle:Order")
     */
    public function transferAction(Order $order, Request $request)
    {
        $em = $this->getEntityManager();
        $bankAccount = $em->getRepository('PaymentBundle:BankAccount')->findAll();
        if (count($bankAccount) > 0) {
            $bankAccount = $bankAccount[0];
        } else {
            $this->setTranslatedFlashMessage('No se puede procesar el pedido mediante transferencia bancaria. Disculpa las molestias', 'error');
            return $this->redirect('ecommerce_homepage');
        }

        $dispatcher = $this->get('event_dispatcher');
        $orderEvent = new OrderEvent($order);
        $dispatcher->dispatch(OrderEvents::NEW_ORDER, $orderEvent);
        $payment = new Transfer();
        $payment->setState(Transfer::REQUESTED);

        $bill = new Bill();
        $bill->setPayment($payment);
        $payment->setBill($bill);

        $payment->setOrder($order);
        $payment->setTotal($order->getTotalAmountWithShipment());
        $order->setPayment($payment);

        $em->persist($order);
        $em->persist($payment);
        $em->persist($bill);
        $em->flush();

        return $this->render('PaymentBundle:Transfer:transfer.html.twig', array('order' => $order, 'bankAccount' => $bankAccount));
    }
}
