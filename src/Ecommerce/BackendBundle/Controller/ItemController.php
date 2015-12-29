<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\BackendBundle\Form\Type\ItemSearchType;
use Ecommerce\ImageBundle\Form\Type\MultipleImageType;
use Ecommerce\ItemBundle\Form\Type\ItemType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\ItemBundle\Entity\Item;
use Symfony\Component\HttpFoundation\Request;

class ItemController extends CustomController
{
    public function listAction(Request $request)
    {
        $em = $this->getEntityManager();
        $form = $this->createForm(new ItemSearchType());
        $form->submit($request);
        $criteria = $this->getCriteriaFromSearchForm($form);

        $items = $em->getRepository("ItemBundle:Item")->findBySearchCriteria($criteria)->getResult();

        return $this->render('BackendBundle:Item:list.html.twig', array('items' => $items, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("item", class="ItemBundle:Item")
     */
    public function viewAction(Item $item, Request $request)
    {
        return $this->render('BackendBundle:Item:view.html.twig', array('item' => $item));
    }

    /**
     * @ParamConverter("item", class="ItemBundle:Item")
     */
    public function editAction(Item $item, Request $request)
    {
        $form = $this->createForm(new ItemType(), $item);
        $imageForm = $this->createForm(new MultipleImageType());
        $handler = $this->get('item.item_form_handler');
        $imagesHandler = $this->get('image.form_handler');

        if ($handler->handle($form, $request)) {
            $imagesHandler->handleMultiple($imageForm, $request, $item);
            $this->setTranslatedFlashMessage('Se ha modificado el producto');

            return $this->redirect($this->generateUrl('admin_item_index'));
        }

        return $this->render('BackendBundle:Item:create.html.twig', array('edition' => true, 'item' => $item, 'form' => $form->createView(), 'formImage' => $imageForm->createView()));
    }

    /**
     * @ParamConverter("item", class="ItemBundle:Item")
     */
    public function deleteAction(Item $item)
    {
        $em = $this->getEntityManager();
        $item->setDeleted(new \DateTime('now'));
        $em->persist($item);
        $em->flush();

        $this->setTranslatedFlashMessage('Se ha eliminado el producto');

        return $this->redirect($this->generateUrl('admin_item_index'));
    }

    public function createAction(Request $request)
    {
        $item = new Item();
        $form = $this->createForm(new ItemType(), $item);
        $handler = $this->get('item.item_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha creado el producto correctamente. ¡Ahora puede subir las imágenes!');

            return $this->redirect($this->generateUrl('admin_item_edit', array('slug' => $item->getSlug())));
        }

        return $this->render('BackendBundle:Item:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("item", class="ItemBundle:Item")
     */
    public function uploadImagesAction(Request $request, Item $item)
    {
        $file = $request->files->get('multiple_images');
        $file = $file['images'][0];

        $formImgHandler = $this->get('image.create_image_asynchronous_form_handler');
        $result = $formImgHandler->handleAjaxUpload($file, $item, $request);

        if (!is_array($result)) {
            $request_result = false;
            $request_pathImage = null;
            $request_idImage = null;
            if ($result == false) {
                $request_msg = $this->getTranslatedMessage("No se ha podido subir la imágen, inténtalo de nuevo");
            } elseif (get_class($result) == "Symfony\\Component\\Validator\\ConstraintViolationList") {
                $request_msg = $result->get(0)->getMessage();
            }
            $deleteImageUrl = null;
            $imageMainUrl = null;
        } else {
            $request_result = true;
            $request_msg = $this->getTranslatedMessage('La foto se ha subido correctamente');
            $request_pathImage = $result['path'];
            $request_idImage = $result['id'];
            $deleteImageUrl = $this->container->get('router')->generate('delete_image',array('id' => $request_idImage), true);
            $imageMainUrl = $this->container->get('router')->generate('change_image_main',array('id' => $request_idImage), true);
        }

        return $response = new \Symfony\Component\HttpFoundation\Response(json_encode(array('request_result'    => $request_result,
            'request_msg'       => $request_msg,
            'request_pathImage' => $request_pathImage,
            'request_pathDelete'=> $deleteImageUrl,
            'request_pathMain'  => $imageMainUrl,
            'request_idImage'   => $request_idImage)));
    }
}