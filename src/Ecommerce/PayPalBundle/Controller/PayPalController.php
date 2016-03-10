<?php
namespace Ecommerce\PayPalBundle\Controller;

use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\OrderBundle\Entity\Order;
use Ecommerce\OrderBundle\Event\OrderEvent;
use Ecommerce\OrderBundle\Event\OrderEvents;
use Ecommerce\PaymentBundle\Entity\Bill;
use Ecommerce\PayPalBundle\Entity\PaypalPayment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PayPalController extends CustomController
{
    /**
     * @ParamConverter("$order", class="OrderBundle:Order")
     */
    public function payWithPayPalAction(Order $order)
    {
        $em = $this->getEntityManager();
        $user = $this->getCurrentUser();
        $paypal = $this->get('paypal');

        $paymentAmount = urlencode($order->getTotalAmountWithoutTaxes());
        $paymentTaxes = urlencode($order->getTotalTaxes());

        $deliveryAmount = urlencode($order->getShipmentCost());
        $desc = urlencode($this->get('translator')->trans(Order::PAYPAL_DESC));

        $urlAccept = $this->generateUrl('paypal_pay_correct', array('id' => $order->getId()), true);
        $urlCancel = $this->generateUrl('paypal_pay_denied', array('id' => $order->getId()), true);

        $response = $paypal->pay($paymentAmount, $paymentTaxes, $deliveryAmount, $desc, $urlAccept, $urlCancel);
        $url = $response['url'];

        $this->clearCart();

        return $this->redirect($url);
    }

    /**
     * @ParamConverter("$order", class="OrderBundle:Order")
     */
    public function prePayCorrectAction(Order $order, Request $request)
    {
        $em = $this->getEntityManager();
        $payerId = $request->query->get('PayerID');
        $token = $request->query->get('token');

        $payment = new PaypalPayment();
        $payment->setPayerId($payerId);
        $payment->setTokenPayPal($token);

        $bill = new Bill();
        $bill->setPayment($payment);
        $payment->setBill($bill);

        $payment->setOrder($order);
        $payment->setTotal($order->getTotalAmount());
        $order->setPayment($payment);

        $em->persist($order);
        $em->persist($payment);
        $em->persist($bill);
        $em->flush();

        //$url = $response['url'];

        return $this->render('PayPalBundle:Paypal:confirm.html.twig', array('token' => $token,
                                                                            'payerId' => $payerId,
                                                                            'order' => $order));
    }

    /**
     * @ParamConverter("$order", class="OrderBundle:Order")
     */
    public function payCorrectAction(Order $order, $token, $payerId, Request $request)
    {
        $paypal = $this->get('paypal');
        $paymentAmount = urlencode($order->getTotalAmountWithoutTaxes());
        $paymentTaxes = urlencode($order->getTotalTaxes());

        $deliveryAmount = urlencode($order->getShipmentCost());

        $response = $paypal->doExpressCheckoutPayment($token, $payerId, $paymentAmount, $paymentTaxes, $deliveryAmount);

        if ($response['ok']) {
            $em = $this->getEntityManager();
            $dispatcher = $this->get('event_dispatcher');
            $orderEvent = new OrderEvent($order);
            $dispatcher->dispatch(OrderEvents::NEW_ORDER, $orderEvent);
            $order->setStatus(Order::STATUS_READY);

            $payment = $order->getPayment();
            $payment->setExpressCheckoutDone(true);
            $em->persist($order);
            $em->persist($payment);
            $em->flush();

            return $this->render('PayPalBundle:Paypal:success.html.twig', array('token' => $token,
                'payerId' => $payerId,
                'order' => $order));
        }

        return $this->redirect($this->generateUrl('paypal_pay_denied', array('id' => $order->getId()), true));
    }

    /**
     * @ParamConverter("$order", class="OrderBundle:Order")
     */
    public function payDeniedAction(Order $order)
    {
        return $this->render('PayPalBundle:Paypal:wrong.html.twig', array('order' => $order));
    }
}