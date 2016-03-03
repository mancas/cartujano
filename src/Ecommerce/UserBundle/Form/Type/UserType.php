<?php
namespace Ecommerce\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'repeated', array(
            'type' => 'email',
            'first_options' => array('attr' => array('placeholder' => 'Email')),
            'second_options' => array('attr' => array('placeholder' => 'Confirmar email')),
            'error_bubbling' => 'true'))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_options' => array('attr' => array('placeholder' => 'Password')),
                'second_options' => array('attr' => array('placeholder' => 'Confirmar password')),
                'error_bubbling' => 'true'))
            ->add('name', 'text', array('required' => true, 'attr' => array('placeholder' => 'Nombre')))
            ->add('lastName', 'text', array('required' => true, 'attr' => array('placeholder' => 'Apellidos')))
            ->add('phone', 'integer');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Ecommerce\UserBundle\Entity\User',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ecommerce\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}