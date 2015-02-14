<?php

namespace Ecommerce\BackendBundle\Controller;

use Ecommerce\PaymentBundle\Entity\BankAccount;
use Ecommerce\PaymentBundle\Form\Type\BankAccountType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ecommerce\FrontendBundle\Controller\CustomController;
use Symfony\Component\HttpFoundation\Request;

class BankAccountController extends CustomController
{
    public function listAction()
    {
        $em = $this->getEntityManager();
        $bankAccount = $em->getRepository('PaymentBundle:BankAccount')->findAll();
        if (count($bankAccount) > 0) {
            $bankAccount = $bankAccount[0];
        } else {
            $bankAccount = null;
        }

        return $this->render('BackendBundle:BankAccount:list.html.twig', array('account' => $bankAccount));
    }

    public function createAction(Request $request)
    {
        $bankAccount = new BankAccount();
        $form = $this->createForm(new BankAccountType(), $bankAccount);
        $handler = $this->get('payment.bankaccount_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha añadido correctamente la cuenta bancaria');

            return $this->redirect($this->generateUrl('admin_bank_account_index'));
        } else {
            $this->setTranslatedFlashMessage('La cuenta bancaria introducida no es válida', 'error');
        }

        return $this->render('BackendBundle:BankAccount:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("account", class="PaymentBundle:BankAccount")
     */
    public function editAction(BankAccount $account, Request $request)
    {
        $form = $this->createForm(new BankAccountType(), $account);
        $handler = $this->get('payment.bankaccount_form_handler');

        if ($handler->handle($form, $request)) {
            $this->setTranslatedFlashMessage('Se ha editado correctamente la cuenta bancaria');

            return $this->redirect($this->generateUrl('admin_bank_account_index'));
        } else {
            $this->setTranslatedFlashMessage('La cuenta bancaria introducida no es válida', 'error');
        }

        return $this->render('BackendBundle:BankAccount:create.html.twig', array('edition' => true, 'account' => $account, 'form' => $form->createView()));
    }

    /**
     * @ParamConverter("account", class="PaymentBundle:BankAccount")
     */
    public function deleteAction(BankAccount $account)
    {
        $em = $this->getEntityManager();
        $em->delete($account);
        $em->flush();
        $this->setTranslatedFlashMessage('Se ha eliminado la cuenta bancaria. Recuerda que tus clientes no podrán utilizar la transferencia bancaria como método de pago.');

        return $this->redirect($this->generateUrl('admin_bank_account_index'));
    }

}
