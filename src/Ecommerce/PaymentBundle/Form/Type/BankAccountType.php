<?php
namespace Ecommerce\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BankAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('iban', 'text', array('required' => true, 'attr' => array('maxLength' => 4)))
                ->add('bankCode', 'text', array('required' => true, 'attr' => array('maxLength' => 4)))
                ->add('branchCode', 'text', array('required' => true, 'attr' => array('maxLength' => 4)))
                ->add('checkDigits', 'text', array('required' => true, 'attr' => array('maxLength' => 2)))
                ->add('accountNumber', 'text', array('required' => true, 'attr' => array('maxLength' => 10)));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Ecommerce\PaymentBundle\Entity\BankAccount',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ecommerce\PaymentBundle\Entity\BankAccount'
        ));
    }

    public function getName()
    {
        return 'bankAccount';
    }
}