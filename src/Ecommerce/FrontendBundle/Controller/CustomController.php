<?php

namespace Ecommerce\FrontendBundle\Controller;

use Ecommerce\CartBundle\Event\CartEvent;
use Ecommerce\CartBundle\Event\CartEvents;
use Ecommerce\CartBundle\Model\Cart;
use Ecommerce\CartBundle\Storage\CartStorageManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;

class CustomController extends Controller
{
    protected function getHttpJsonResponse($jsonResponse)
    {
        $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    protected function setTranslatedFlashMessage($message, $class = 'info')
    {
        $translatedMessage = $this->get('translator')->trans($message);
        $this->get('session')->getFlashBag()->set($class, $translatedMessage);
    }

    protected function getTranslatedMessage($message)
    {
        return $this->get('translator')->trans($message);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    protected function getCriteriaFromSearchForm($form)
    {
        $criteria = array();
        if ($form->isValid()) {
            $criteria = $form->getData();
        }

        return $criteria;
    }

    protected function getCurrentUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    protected function getCartStorageManager()
    {
        return $this->get('cart.cart_storage_manager');
    }

    protected function setCurrentCartIfNeeded()
    {
        $cartStorageManager = $this->getCartStorageManager();
        if (!$cartStorageManager->getCurrentCart())
        {
            $cart = new Cart();
            $now = new \DateTime();
            $now->modify('+ 1day');
            $cart->setExpiredAt($now);
            $cartEvent = new CartEvent($cart);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(CartEvents::NEW_CART, $cartEvent);
        }
    }

    protected function renderLoginTemplate($template, Request $request)
    {
        $session = $request->getSession();
        $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR, $session->get(SecurityContext::AUTHENTICATION_ERROR));

        return $this->render($template, array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error));
    }

    protected function resetToken($user, $provider = 'user')
    {
        $token = new UsernamePasswordToken($user, null, $provider, $user->getRoles());
        $this->container->get('security.context')->setToken($token);
        $this->container->get('session')->set("_security_private", serialize($token));
    }

    protected function clearCart()
    {
        $cartEvent = new CartEvent($this->getCartStorageManager()->getCurrentCart());
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(CartEvents::CLEAR_CART, $cartEvent);
    }
}