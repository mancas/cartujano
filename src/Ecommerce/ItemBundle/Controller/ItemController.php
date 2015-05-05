<?php

namespace Ecommerce\ItemBundle\Controller;

use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\ItemBundle\Entity\Item;
use Ecommerce\ItemBundle\Entity\Shipment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class ItemController extends CustomController
{
    /**
     * @param Item $item
     * @param Shipment $shipment
     *
     * @Template("ItemBundle:Commons:item-box.html.twig")
     *
     * @return array
     */
    public function itemBoxAction(Item $item, $shipment, $isEcommerceAction)
    {
        return array('item' => $item, 'shipment' => $shipment, 'isEcommerceAction' => $isEcommerceAction);
    }
}
