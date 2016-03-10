<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\UserBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\UserBundle\Entity\User;

class UserController extends CustomController
{
    public function listAction()
    {
        $em = $this->getEntityManager();
        $users = $em->getRepository("UserBundle:User")->findAll();

        return $this->render("BackendBundle:User:list.html.twig", array('users' => $users));
    }

    /**
     * @ParamConverter("user", class="UserBundle:User")
     */
    public function viewAction(User $user)
    {
        return $this->render("BackendBundle:User:view.html.twig", array('user' => $user));
    }

    /**
     * @ParamConverter("user", class="UserBundle:User")
     */
    public function editAction(User $user, Request $request)
    {
        $form = $this->createForm(new UserType(), $user);
        $handler = $this->get('user.user_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha modificado el usuario');

            $this->redirect($this->generateUrl('admin_user_index'));
        }

        return $this->render("BackendBundle:User:create.html.twig", array('edit' => true, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("user", class="UserBundle:User")
     */
    public function deleteAction(User $user)
    {
        $em = $this->getEntityManager();
        $user->setDeletedDate(new \DateTime('now'));
        $em->persist($user);
        $em->flush();

        $this->setTranslatedFlashMessage('Se ha eliminado el usuario');

        return $this->redirect($this->generateUrl('admin_user_index'));
    }

    /**
     * @ParamConverter("user", class="UserBundle:User")
     */
    public function validateAction(User $user)
    {
        $em = $this->getEntityManager();
        $user->setValidated(true);
        $em->persist($user);
        $em->flush();

        $this->setTranslatedFlashMessage('Se ha validado al usuario');

        return $this->redirect($this->generateUrl('admin_user_index'));
    }

    /**
     * @ParamConverter("user", class="UserBundle:User")
     */
    public function reactivateAction(User $user)
    {
        $em = $this->getEntityManager();
        $user->setDeletedDate(null);
        $em->persist($user);
        $em->flush();

        $this->setTranslatedFlashMessage('Se ha reactivado el usuario');

        return $this->redirect($this->generateUrl('admin_user_index'));
    }
}