<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\BackendBundle\Form\Type\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Symfony\Component\HttpFoundation\Request;
use Ecommerce\CategoryBundle\Entity\Category;

class CategoryController extends CustomController
{
    public function listAction()
    {
        $em = $this->getEntityManager();
        $categories = $em->getRepository('CategoryBundle:Category')->findAll();

        return $this->render('BackendBundle:Category:list.html.twig', array('categories' => $categories));
    }

    /**
     * @ParamConverter("category", class="CategoryBundle:Category")
     */
    public function editAction(Category $category, Request $request)
    {
        $form = $this->createForm(new CategoryType(), $category);
        $handler = $this->get('category.category_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha modificado la categoría');

            return $this->redirect($this->generateUrl('admin_category_index'));
        }

        return $this->render('BackendBundle:Category:create.html.twig', array('edition' => true, 'category' => $category, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("category", class="CategoryBundle:Category")
     */
    public function deleteAction(Category $category)
    {
        $em = $this->getEntityManager();
        $now = new \DateTime('now');
        $category->setDeleted($now);
        foreach ($category->getSubcategories() as $subcategory) {
            foreach ($subcategory->getItems() as $item) {
                $item->setDeleted($now);
                $em->persist($item);
            }

            $subcategory->setDeleted($now);
            $em->persist($subcategory);
        }
        $em->persist($category);
        $em->flush();

        $this->setTranslatedFlashMessage('Se ha eliminado la categoría');

        return $this->redirect($this->generateUrl('admin_category_index'));
    }

    public function createAction(Request $request)
    {
        $form = $this->createForm(new CategoryType());
        $handler = $this->get('category.category_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha creado la categoría correctamente');

            return $this->redirect($this->generateUrl('admin_category_index'));
        }

        return $this->render('BackendBundle:Category:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("category", class="CategoryBundle:Category")
     */
    public function changeUseInIndexAction(Category $category)
    {
        $em = $this->getEntityManager();

        if ($category->getUseInIndex()) {
            $category->setUseInIndex(false);
            $this->setTranslatedFlashMessage('La categoría ahora no se muestra en la página principal. Si no tienes 3 categorías mostrandose en la página principal, se utilizarán otras categorías según su fecha de creación');
        } else {
            $category->setUseInIndex(true);
            $this->setTranslatedFlashMessage('La categoría se muestra ahora en la página principal. Ten en cuenta que se mostrarán solo 3 categorías ordenadas según la fecha de modificación');
        }

        $em->persist($category);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_category_index'));
    }

    /**
     * @ParamConverter("category", class="CategoryBundle:Category")
     */
    public function viewAction(Category $category)
    {
        return $this->render('BackendBundle:Category:view.html.twig', array('category' => $category));
    }
}
