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

        $paymentAmount = urlencode(round($order->getTotalAmount(), 2));

        $deliveryAmount = urlencode($order->getShipment()->getCost());
        $desc = urlencode($this->get('translator')->trans(Order::PAYPAL_DESC));

        $urlAccept = $this->generateUrl('paypal_pay_correct', array('id' => $order->getId()), true);
        $urlCancel = $this->generateUrl('paypal_pay_denied', array('id' => $order->getId()), true);

        $response = $paypal->pay($paymentAmount, $deliveryAmount, $desc, $urlAccept, $urlCancel);
        $url = $response['url'];

        $this->clearCart();

        return $this->redirect($url);
    }

    /**
     * @ParamConverter("$order", class="OrderBundle:Order")
     */
    public function payCorrectAction(Order $order, Request $request)
    {
        $em = $this->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $payerId = $request->query->get('PayerID');
        $token = $request->query->get('token');
        $result = true;

        $dispatcher = $this->get('event_dispatcher');
        $orderEvent = new OrderEvent($order);
        $dispatcher->dispatch(OrderEvents::NEW_ORDER, $orderEvent);
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

        return $this->render('PayPalBundle:Paypal:success.html.twig', array('result' => $result));
    }

    /**
     * @ParamConverter("$order", class="OrderBundle:Order")
     */
    public function payDeniedAction(Order $order)
    {
        return $this->render('PayPalBundle:Paypal:wrong.html.twig', array('order' => $order));
    }
}