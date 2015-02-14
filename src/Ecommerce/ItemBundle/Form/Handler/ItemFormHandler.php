<?php

namespace Ecommerce\ItemBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Ecommerce\ItemBundle\Entity\Item;

class ItemFormHandler
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function handle(FormInterface $form, Request $request)
    {
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $item = $form->getData();

                $this->em->persist($item);
                $this->em->flush();
                return true;
            }
        }

        return false;
    }
}