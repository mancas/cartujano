<?php

namespace Ecommerce\BackendBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ecommerce\OrderBundle\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('from', 'date', array('widget' => 'single_text', 'required' => false, 'format' => 'dd-MM-yyyy'))
                ->add('to', 'date', array('widget' => 'single_text', 'required' => false, 'format' => 'dd-MM-yyyy'))
                ->add('payment', 'checkbox', array('required' => false))
                ->add('status', 'choice', array(
                'choices' => array(Order::STATUS_READY => Order::STATUS_READY, Order::STATUS_IN_PROCESS => Order::STATUS_IN_PROCESS, Order::STATUS_OUT_OF_STOCK => Order::STATUS_OUT_OF_STOCK,
                                    Order::STATUS_READY_TO_TAKE => Order::STATUS_READY_TO_TAKE, Order::STATUS_SEND => Order::STATUS_SEND, Order::STATUS_CANCELED => Order::STATUS_CANCELED),
                'empty_value' => 'Estado',
                'required' => false
                 ))
                ->add('shipment', 'entity',
                    array(
                        'class' => 'ItemBundle:Shipment',
                        'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('s')->orderBy('s.cost', 'ASC');
                            },
                        'expanded' => false,
                        'required' => false,
                        'empty_value' => 'Método de envío'
                    )
                );
    }

    public function getName()
    {
        return 'form_search_order';
    }
}
