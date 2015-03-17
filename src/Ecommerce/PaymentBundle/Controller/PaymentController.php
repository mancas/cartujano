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

    }
}
