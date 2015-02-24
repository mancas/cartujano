<?php

namespace Ecommerce\CategoryBundle\Controller;

use Ecommerce\CategoryBundle\Entity\Category;
use Ecommerce\CategoryBundle\Entity\Subcategory;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CategoryController extends CustomController
{
    public function viewCategoriesAction()
    {
        $em = $this->getEntityManager();
        $categories = $em->getRepository('CategoryBundle:Category')->findCategoriesDQL();

        return $this->render('CategoryBundle:Category:list.html.twig', array('categories' => $categories));
    }

    /**
     * @ParamConverter("category", class="CategoryBundle:Category")
     */
    public function viewSubcategoriesAction(Category $category)
    {
        $em = $this->getEntityManager();
        $subcategories =
            $em->getRepository('CategoryBundle:Subcategory')->findSubcategoriesFromParentCategoryDQL($category->getId());
        return $this->render('CategoryBundle:Subcategory:list.html.twig',
            array('subcategories' => $subcategories,
                  'category' => $category->getName()));
    }

    /**
     * @ParamConverter("subcategory", class="CategoryBundle:Subcategory")
     */
    public function viewSubcategoryAction(Subcategory $subcategory)
    {
        $em = $this->getEntityManager();
        $paginator = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage(32, 'items');
        $items = $paginator->paginate($em->getRepository('ItemBundle:Item')->findItemsBySubcategoryDQL($subcategory), 'items')->getResult();

        return $this->render('CategoryBundle:Subcategory:items-list.html.twig', array('items' => $items, 'paginator' => $paginator, 'subcategory' => $subcategory));
    }
}
