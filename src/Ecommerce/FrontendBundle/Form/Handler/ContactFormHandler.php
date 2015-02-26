<?php

namespace Ecommerce\FrontendBundle\Form\Handler;

use Ecommerce\FrontendBundle\Event\ContactEvent;
use Ecommerce\FrontendBundle\Event\ContactEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;

class ContactFormHandler
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(FormInterface $form, Request $request)
    {
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $contact = $form->getData();
                $event = new ContactEvent($contact);
                try {
                    $this->eventDispatcher->dispatch(ContactEvents::CONTACT_CREATED, $event);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }

                return 'Gracias por contactar con El Cartujano. Te contestaremos en la mayor brevedad posible.';
            }
        }

        return "";
    }
}