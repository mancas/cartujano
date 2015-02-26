<?php

namespace Ecommerce\FrontendBundle\EventListener;

use Ecommerce\FrontendBundle\Event\ContactEvent;
use Ecommerce\FrontendBundle\Event\ContactEvents;
use Ecommerce\FrontendBundle\Model\Contact;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;

class ContactListener implements EventSubscriberInterface
{
    private $mailer;
    private $templating;
    private $direction;

    public static function getSubscribedEvents()
    {
        return array(
            ContactEvents::CONTACT_CREATED => 'onNewContactCreated'
        );
    }

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, $direction)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->direction = $direction;
    }

    public function onNewContactCreated(ContactEvent $event)
    {
        $this->sendContactEmail($event->getContact());
    }

    private function sendContactEmail(Contact $contact)
    {
        $messageBody = $this->templating->render('FrontendBundle:Email:contact.html.twig', array('contact' => $contact));

        $message = $this->mailer->createMessage()
            ->setSubject('Contacto desde el formulario de elcartujano.es')
            ->setFrom($contact->getEmail())
            ->setTo($this->direction)
            ->setBody($messageBody, 'text/html');

        if (!$this->mailer->send($message)) {
            throw new \Exception('Error en el envío del correo de contacto');
        }
    }
}