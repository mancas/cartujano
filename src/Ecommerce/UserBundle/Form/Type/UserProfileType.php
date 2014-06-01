<?php
namespace Ecommerce\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => true, 'attr' => array('placeholder' => 'Nombre')))
                ->add('lastName', 'text', array('required' => true, 'attr' => array('placeholder' => 'Apellidos')))
                ->add('nif', 'text', array('required' => true, 'attr' => array('placeholder' => 'Nif')));
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
        return 'user_profile';
    }
}