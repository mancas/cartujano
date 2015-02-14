<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\ItemBundle\Entity\Shipment;
use Ecommerce\ItemBundle\Form\Type\ShipmentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Symfony\Component\HttpFoundation\Request;

class ShipmentController extends CustomController
{
    public function listAction(Request $request)
    {
        $em = $this->getEntityManager();
        $shipmentOptions = $em->getRepository("ItemBundle:Shipment")->findAllShipmentOptions();

        return $this->render('BackendBundle:Shipment:list.html.twig', array('shipmentOptions' => $shipmentOptions));
    }

    /**
     * @ParamConverter("shipment", class="ItemBundle:Shipment")
     */
    public function editAction(Delivery $shipment, Request $request)
    {
        $form = $this->createForm(new ShipmentType(), $shipment);
        $handler = $this->get('shipment.shipment_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha modificado la opción de envío');

            return $this->redirect($this->generateUrl('admin_shipment_index'));
        }

        return $this->render('BackendBundle:Shipment:create.html.twig', array('edition' => true, 'shipmentOption' => $shipment, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("shipment", class="ItemBundle:Shipment")
     */
    public function deleteAction(Shipment $shipment)
    {
        $em = $this->getEntityManager();
        $em->remove($shipment);
        $em->flush();

        $this->setTranslatedFlashMessage('Se ha eliminado la opción de envío');

        return $this->redirect($this->generateUrl('admin_shipment_index'));
    }

    public function createAction(Request $request)
    {
        $shipmentOption = new Shipment();
        $form = $this->createForm(new ShipmentType(), $shipmentOption);
        $handler = $this->get('shipment.shipment_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha creado la opción de envío correctamente');

            return $this->redirect($this->generateUrl('admin_shipment_index'));
        }

        return $this->render('BackendBundle:Shipment:create.html.twig', array('form' => $form->createView()));
    }
}