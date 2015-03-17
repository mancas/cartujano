<?php

namespace Ecommerce\OrderBundle\Controller;

use Ecommerce\CartBundle\Event\CartEvent;
use Ecommerce\CartBundle\Event\CartEvents;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\OrderBundle\Entity\Order;
use Ecommerce\OrderBundle\Entity\OrderItem;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends CustomController
{
    const PAYPAL = 0;
    const TRANSFER = 1;

    public function newOrderAction(Request $request)
    {
        $em = $this->getEntityManager();
        $user = $this->getCurrentUser();
        $cartStorageManager = $this->getCartStorageManager();
        $cart = $cartStorageManager->getCurrentCart();

        $shipmentOptions = $em->getRepository('ItemBundle:Shipment')->findAllShipmentOptions();

        if ($request->isMethod('POST')) {
            $handler = $this->get('order.new_order_form_handler');
            $handleResult = $handler->handle($user, $cart, $request);
            if ($handleResult['result']) {
                // TODO: paypal or transference
                switch ((int) $handleResult['payment']) {
                    case PAYPAL:
                        return $this->redirect($this->generateUrl('pay_paypal', array('id' => $handleResult['order'])));
                    case TRANSFER:
                        return $this->redirect($this->generateUrl('pay_transfer', array('id' => $handleResult['order'])));
                }
            }
            $this->setTranslatedFlashMessage('Por favor, revisa la información introducida y asegurate de seleccionar una forma de pago y una dirección de envío.', 'error');
        }
        return $this->render('OrderBundle:Order:new-order.html.twig', array('cart' => $cart,
                                                                            'user' => $user,
                                                                            'shipmentOptions' => $shipmentOptions));
    }
}
