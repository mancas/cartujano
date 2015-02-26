<?php

namespace Ecommerce\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => true))
                ->add('email', 'email', array('required' => true))
                ->add('text', 'textarea', array('required' => true));
    }

    public function getName()
    {
        return 'contact';
    }
}