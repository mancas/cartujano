<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\BackendBundle\Form\Type\CategoryType;
use Ecommerce\CategoryBundle\Entity\Category;
use Ecommerce\CategoryBundle\Form\Type\SubcategorySearchType;
use Ecommerce\CategoryBundle\Form\Type\SubcategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Symfony\Component\HttpFoundation\Request;
use Ecommerce\CategoryBundle\Entity\Subcategory;

class SubcategoryController extends CustomController
{

    public function listAction(Request $request)
    {
        $em = $this->getEntityManager();
        $form = $this->createForm(new SubcategorySearchType());
        $form->submit($request);
        $criteria = $this->getCriteriaFromSearchForm($form);

        $subcategories = $em->getRepository('CategoryBundle:Subcategory')->findBySearchCriteria($criteria)->getResult();

        return $this->render('BackendBundle:Subcategory:list.html.twig', array('subcategories' => $subcategories, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("subcategory", class="CategoryBundle:Subcategory")
     */
    public function editAction(Subcategory $subcategory, Request $request)
    {
        $form = $this->createForm(new SubcategoryType(), $subcategory);
        $handler = $this->get('subcategory.subcategory_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha modificado la subcategoría');

            return $this->redirect($this->generateUrl('admin_subcategory_index'));
        }

        return $this->render('BackendBundle:Subcategory:create.html.twig', array('edition' => true, 'subcategory' => $subcategory, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("subcategory", class="CategoryBundle:Subcategory")
     */
    public function deleteAction(Subcategory $subcategory)
    {
        $em = $this->getEntityManager();
        $items = $subcategory->getItems();
        foreach ($items as $item) {
            $item->setSubcategory(null);
            $em->persist($item);
        }
        $em->remove($subcategory);
        $em->flush();

        $this->setTranslatedFlashMessage('Se ha eliminado la subcategoría');

        return $this->redirect($this->generateUrl('admin_subcategory_index'));
    }

    public function createAction(Request $request)
    {
        $form = $this->createForm(new SubcategoryType());
        $handler = $this->get('subcategory.subcategory_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha creado la subcategoría correctamente');

            return $this->redirect($this->generateUrl('admin_subcategory_index'));
        }

        return $this->render('BackendBundle:Subcategory:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("category", class="CategoryBundle:Category")
     */
    public function viewAction(Category $category)
    {
        return $this->render('BackendBundle:Subcategory:view.html.twig', array('category' => $category));
    }
}
