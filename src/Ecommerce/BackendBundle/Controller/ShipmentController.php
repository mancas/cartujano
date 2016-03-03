<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\ItemBundle\Entity\Extra;
use Ecommerce\ItemBundle\Entity\Shipment;
use Ecommerce\ItemBundle\Form\Type\ExtraType;
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
        $extraOptions = $em->getRepository("ItemBundle:Extra")->findAllExtraOptions();

        return $this->render('BackendBundle:Shipment:list.html.twig', array('shipmentOptions' => $shipmentOptions, 'extraOptions' => $extraOptions));
    }

    /**
     * @ParamConverter("shipment", class="ItemBundle:Shipment")
     */
    public function editAction(Shipment $shipment, Request $request)
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
        $shipment->setDeleted(new \DateTime('now'));
        $em->persist($shipment);
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

    public function createExtraAction(Request $request)
    {
        $extraOption = new Extra();
        $form = $this->createForm(new ExtraType(), $extraOption);
        $handler = $this->get('extra.extra_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha creado el suplemento correctamente');

            return $this->redirect($this->generateUrl('admin_shipment_index'));
        }

        return $this->render('BackendBundle:Shipment:create-extra.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("extra", class="ItemBundle:Extra")
     */
    public function editExtraAction(Extra $extra, Request $request)
    {
        $form = $this->createForm(new ExtraType(), $extra);
        $handler = $this->get('extra.extra_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha modificado el suplemento');

            return $this->redirect($this->generateUrl('admin_shipment_index'));
        }

        return $this->render('BackendBundle:Shipment:create-extra.html.twig', array('edition' => true, 'extraOption' => $extra, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("extra", class="ItemBundle:Extra")
     */
    public function deleteExtraAction(Extra $extra)
    {
        $em = $this->getEntityManager();
        $extra->setDeleted(new \DateTime('now'));
        $em->persist($extra);
        $em->flush();

        $this->setTranslatedFlashMessage('Se ha eliminado el suplemento');

        return $this->redirect($this->generateUrl('admin_shipment_index'));
    }
}