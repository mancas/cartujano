<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\ItemBundle\Entity\ItemPackage;
use Ecommerce\ItemBundle\Form\Type\ItemPackageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\ItemBundle\Entity\Item;
use Symfony\Component\HttpFoundation\Request;
use Ecommerce\ItemBundle\Form\Handler\ItemPackageFormHandler;

class ItemPackageController extends CustomController
{
    public function listAction(Request $request)
    {
        $em = $this->getEntityManager();
        $itemPackages = $em->getRepository("ItemBundle:ItemPackage")->findAll();

        return $this->render('BackendBundle:ItemPackage:list.html.twig', array('packages' => $itemPackages));
    }

    /**
     * @ParamConverter("package", class="ItemBundle:ItemPackage")
     */
    public function editAction(ItemPackage $package, Request $request)
    {
        $form = $this->createForm(new ItemPackageType(), $package);
        $handler = $this->get('item.item_package_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha modificado el tipo de paquete correctamente');

            return $this->redirect($this->generateUrl('admin_item_package_index'));
        }

        return $this->render('BackendBundle:ItemPackage:create.html.twig', array('edition' => true, 'package' => $package, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("package", class="ItemBundle:ItemPackage")
     */
    public function deleteAction(ItemPackage $package)
    {
        $em = $this->getEntityManager();
        $items = $em->getRepository('ItemBundle:Item')->findItemsByPackageDQL($package->getId())->getResult();
        foreach ($items as $item) {
            $item->setPackage(null);
            $em->persist($item);
        }
        $em->flush();

        $em->remove($package);
        $em->flush();

        $this->setTranslatedFlashMessage('Se ha eliminado el tipo de paquete');

        return $this->redirect($this->generateUrl('admin_item_package_index'));
    }

    public function createAction(Request $request)
    {
        $package = new ItemPackage();
        $form = $this->createForm(new ItemPackageType(), $package);
        $handler = $this->get('item.item_package_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha creado el tipo de paquete correctamente');

            return $this->redirect($this->generateUrl('admin_item_package_index'));
        }

        return $this->render('BackendBundle:ItemPackage:create.html.twig', array('form' => $form->createView()));
    }
}