<?php

namespace Ecommerce\PaymentBundle\Controller;

use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\OrderBundle\Entity\Order;
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
        return $this->render('PaymentBundle:Transfer:transfer.html.twig', array('order' => $order, 'bankAccount' => $bankAccount));
    }
}
