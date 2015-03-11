<?php

namespace Ecommerce\ItemBundle\Controller;

use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\ItemBundle\Entity\Item;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ItemController extends CustomController
{
    /**
     * @param Item $item
     *
     * @Template("ItemBundle:Commons:item-box.html.twig")
     *
     * @return array
     */
    public function itemBoxAction(Item $item)
    {
        return array('item' => $item);
    }
}
