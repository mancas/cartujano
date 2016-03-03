<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\OrderBundle\Entity\Order;
use Ecommerce\PaymentBundle\Entity\Transfer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends CustomController
{
    public function listAction()
    {
        $em = $this->getEntityManager();
        $payments = $em->getRepository('PaymentBundle:Payment')->findPaymentsDQL();
        $paymentsThisMonth = $em->getRepository('PaymentBundle:Payment')->findPaymentsByMonth(new \DateTime('now'));
        $paymentTotal = $this->getTotalAmountFromPayments($payments);
        $paymentMonthly = $this->getTotalAmountFromPayments($paymentsThisMonth);

        $paginator = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage(15, 'payments');
        $payments = $paginator->paginate($em->getRepository('PaymentBundle:Payment')->findPaymentsDQLPaginate(), 'payments')->getResult();

        return $this->render('BackendBundle:Payment:list.html.twig', array('payments' => $payments,
                                                                           'paymentTotal' => $paymentTotal,
                                                                           'paymentMonthly' => $paymentMonthly,
                                                                           'paginator' => $paginator));
    }

    /**
     * @ParamConverter("payment", class="PaymentBundle:Transfer")
     */
    public function validateAction(Transfer $payment)
    {
        $em = $this->getEntityManager();
        $payment->setState(Transfer::PAID);
        $order = $payment->getOrder();
        $order->setStatus(Order::STATUS_READY);
        $em->persist($payment);
        $em->persist($order);
        $em->flush();

        $dispatcher = $this->get('event_dispatcher');
        $orderEvent = new OrderEvent($order);
        $dispatcher->dispatch(OrderEvents::STATUS_CHANGED, $orderEvent);

        $this->setTranslatedFlashMessage('El pago ha sido validado. Recuerda envíar el pedido y marcarlo como enviado para notificar al usuario.');

        return $this->redirect($this->generateUrl('admin_payment_index'));
    }

    private function getTotalAmountFromPayments($payments)
    {
        $totalAmount = 0.0;

        foreach ($payments as $payment) {
            $totalAmount += $payment->getTotal();
        }

        return $totalAmount;
    }
}
