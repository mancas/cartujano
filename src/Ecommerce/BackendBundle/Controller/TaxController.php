<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\ItemBundle\Entity\Tax;
use Ecommerce\ItemBundle\Form\Type\TaxType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Symfony\Component\HttpFoundation\Request;

class TaxController extends CustomController
{
    public function listAction()
    {
        $em = $this->getEntityManager();
        $taxes = $em->getRepository('ItemBundle:Tax')->findAll();

        return $this->render('BackendBundle:Tax:list.html.twig', array('taxes' => $taxes));
    }

    public function createAction(Request $request)
    {
        $tax = new Tax();
        $form = $this->createForm(new TaxType(), $tax);
        $handler = $this->get('tax.tax_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha añadido correctamente el nuevo impuesto');

            return $this->redirect($this->generateUrl('admin_tax_index'));
        } else {
            if ($request->isMethod('POST'))
                $this->setTranslatedFlashMessage('El impuesto añadido no es válido', 'error');
        }

        return $this->render('BackendBundle:Tax:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("tax", class="ItemBundle:Tax")
     */
    public function editAction(Tax $tax, Request $request)
    {
        $form = $this->createForm(new TaxType(), $tax);
        $handler = $this->get('tax.tax_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha editado correctamente el impuesto');

            return $this->redirect($this->generateUrl('admin_tax_index'));
        } else {
            if ($request->isMethod('POST'))
                $this->setTranslatedFlashMessage('El impuesto añadido no es válido', 'error');
        }

        return $this->render('BackendBundle:Tax:create.html.twig', array('edition' => true, 'tax' => $tax, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("tax", class="ItemBundle:Tax")
     */
    public function deleteAction(Tax $tax)
    {
        $em = $this->getEntityManager();
        $items = $em->getRepository('ItemBundle:Item')->findBy(array('tax_id' => $tax->getId()));
        foreach ($items as $item) {
            $item->setTax(null);
            $em->persist($item);
        }
        $em->flush();

        $em->remove($tax);
        $em->flush();
        $this->setTranslatedFlashMessage('Se ha eliminado correctamente el impuesto. Recuerda actualizar el impuesto de todos aquellos productos a los que se les aplicaba.');

        return $this->redirect($this->generateUrl('admin_tax_index'));
    }

    public function getTaxesJSONAction()
    {
        $em = $this->getEntityManager();
        $taxes = $em->getRepository('ItemBundle:Tax')->findAll();
        $taxesValues = array();

        foreach($taxes as $tax) {
            $taxesValues[$tax->getName()] = $tax->getTaxes();
        }

        $jsonResponse = json_encode($taxesValues);

        return $this->getHttpJsonResponse($jsonResponse);
    }
}
