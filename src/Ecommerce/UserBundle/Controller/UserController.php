<?php

namespace Ecommerce\UserBundle\Controller;

use Ecommerce\FrontendBundle\Controller\CustomController;
use Ecommerce\LocationBundle\Form\Type\AddressType;
use Ecommerce\LocationBundle\Entity\Address;
use Ecommerce\OrderBundle\Entity\Order;
use Ecommerce\UserBundle\Entity\User;
use Ecommerce\UserBundle\Form\Type\UserProfileType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class UserController extends CustomController
{
    public function profileAction()
    {
        $em = $this->getEntityManager();
        $user = $this->getCurrentUser();
        if (!$user->getValidated()) {
            return $this->redirect($this->generateUrl('validate_user'));
        }
        $form = $this->createForm(new UserProfileType(), $user);
        $addressForm = $this->createForm(new AddressType());
        $provinces = $em->getRepository('LocationBundle:Province')->findAll();

        if (!$user->isProfileComplete()) {
            $this->setTranslatedFlashMessage('Recuerda rellenar tus datos personales para poder realizar compras');
        }

        return $this->render('UserBundle:User:profile.html.twig', array('user' => $user,
                                                                        'form' => $form->createView(),
                                                                        'addressForm' => $addressForm->createView(),
                                                                        'provinces' => $provinces));
    }

    public function ordersAction()
    {
        $em = $this->getEntityManager();
        $user = $this->getCurrentUser();
        if (!$user->getValidated()) {
            return $this->redirect($this->generateUrl('validate_user'));
        }

        $paginator = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage(15, 'user_orders');
        $orders = $paginator->paginate($em->getRepository('OrderBundle:Order')->findOrdersByUserEmailDQL($user->getEmail()), 'user_orders')->getResult();

        if (!$user->isProfileComplete()) {
            $this->setTranslatedFlashMessage('Recuerda rellenar tus datos personales para poder realizar compras');
        }

        return $this->render('UserBundle:User:orders.html.twig', array('user' => $user, 'orders' => $orders, 'paginator' => $paginator));
    }

    /**
     * @ParamConverter("order", class="OrderBundle:Order")
     */
    public function viewOrderAction(Order $order)
    {
        $user = $this->getCurrentUser();
        $this->setStatusFlashMessage($order);
        $bankAccount = $this->getEntityManager()->getRepository('PaymentBundle:BankAccount')->findBankAccount();
        $bankAccountAvailable = isset($bankAccount);

        if ($bankAccountAvailable) {
            $bankAccount = $bankAccount[0];
        }

        return $this->render('UserBundle:User:view-order.html.twig', array('order' => $order, 'user'=> $user, 'bankAccountAvailable' => $bankAccountAvailable, 'bankAccount' => $bankAccount));
    }

    private function setStatusFlashMessage(Order $order)
    {
        if ($order->getStatus() == Order::STATUS_SEND) {
            $this->setTranslatedFlashMessage('¡Ya hemos enviado tu pedido!');
        } else {
            if ($order->getStatus() == Order::STATUS_READY_TO_TAKE) {
                $this->setTranslatedFlashMessage('Ya puedes recoger tu pedido en nuestra tienda');
            }
        }
    }

    public function editProfileAction(Request $request)
    {
        $jsonResponse = json_encode(array('ok' => false));
        if ($request->isXmlHttpRequest()) {
            $user = $this->getCurrentUser();
            $form = $this->createForm(new UserProfileType(), $user);
            $handler = $this->get('user.user_form_handler');
            if ($handler->handle($form, $request)) {
                $jsonResponse = json_encode(array('ok' => true));
            }
        }

        return $this->getHttpJsonResponse($jsonResponse);
    }

    public function newAddressAction(Request $request)
    {
        $jsonResponse = json_encode(array('ok' => false));
        if ($request->isXmlHttpRequest()) {
            $user = $this->getCurrentUser();
            $form = $this->createForm(new AddressType());
            $handler = $this->get('user.new_address_handler');
            if ($handler->handle($form, $request, $user)) {
                $jsonResponse = json_encode(array('ok' => true));
            }
        }

        return $this->getHttpJsonResponse($jsonResponse);
    }

    /**
     * @ParamConverter("address", class="LocationBundle:Address")
     */
    public function changeMainAddressAction(Address $address, Request $request)
    {
        $jsonResponse = json_encode(array('ok' => false));
        if ($request->isXmlHttpRequest()) {
            $user = $this->getCurrentUser();
            $em = $this->getEntityManager();
            $currentMain = $user->getMainAddress();
            if ($currentMain) {
                $currentMain->setMain(false);
                $em->persist($currentMain);
            }

            $address->setMain(true);
            $em->persist($address);
            $em->flush();
            $jsonResponse = json_encode(array('ok' => true));
        }

        return $this->getHttpJsonResponse($jsonResponse);
    }

    /**
     * @ParamConverter("address", class="LocationBundle:Address")
     */
    public function deleteAddressAction(Address $address, Request $request)
    {
        $jsonResponse = json_encode(array('ok' => false));
        if ($request->isXmlHttpRequest()) {
            $em = $this->getEntityManager();
            $address->setDeleted(new \DateTime('now'));

            $em->persist($address);
            $em->flush();
            $jsonResponse = json_encode(array('ok' => true));
        }

        return $this->getHttpJsonResponse($jsonResponse);
    }

    /**
     * @param User $user
     * @Template("UserBundle:Commons:addressList.html.twig")
     *
     * @return array
     */
    public function adressListAction(User $user, $admin = false)
    {
        $em = $this->getEntityManager();
        $addresses = $em->getRepository('LocationBundle:Address')->findAddressesByUser($user);
        return array('addresses' => $addresses, 'admin' => $admin);
    }
}
