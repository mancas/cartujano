<?php

namespace Ecommerce\PaymentBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Constraints as Assert;

class BankAccountFormHandler
{
    private $em;
    private $validator;

    public function __construct(EntityManager $em, RecursiveValidator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function handle(FormInterface $form, Request $request)
    {
        if ($request->isMethod('POST')) {
            $ibanConstraint = new Assert\Iban();
            $form->handleRequest($request);

            if ($form->isValid()) {
                $bankAccount = $form->getData();
                $errorList = $this->validator->validateValue($bankAccount->getBankAccount(), $ibanConstraint);
                if (count($errorList) === 0) {
                    $this->em->persist($bankAccount);
                    $this->em->flush();

                    return true;
                }
                return false;
            }
        }

        return false;
    }
}