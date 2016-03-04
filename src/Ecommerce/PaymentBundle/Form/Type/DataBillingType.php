<?php
namespace Ecommerce\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DataBillingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false))
                ->add('corporateName', 'text', array('required' => false))
                ->add('phone', 'text', array('required' => false))
                ->add('fax', 'text', array('required' => false))
                ->add('email', 'email', array('required' => false));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Ecommerce\PaymentBundle\Entity\DataBilling',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ecommerce\PaymentBundle\Entity\DataBilling'
        ));
    }

    public function getName()
    {
        return 'dataBilling';
    }
}