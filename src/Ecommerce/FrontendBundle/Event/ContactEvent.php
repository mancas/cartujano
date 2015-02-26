<?php

namespace Ecommerce\FrontendBundle\Event;

use Ecommerce\FrontendBundle\Model\Contact;
use Symfony\Component\EventDispatcher\Event;

class ContactEvent extends Event
{
    private $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return mixed
     */
    public function getContact()
    {
        return $this->contact;
    }
}