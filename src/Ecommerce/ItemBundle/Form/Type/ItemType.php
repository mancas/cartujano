<?php

namespace Ecommerce\ItemBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => true))
                ->add('reference', 'text', array('required' => true))
                ->add('description', 'textarea', array('required' => true))
                ->add('price', 'number', array('required' => false))
                ->add('weight', 'number', array('required' => false))
                ->add('quantity', 'number', array('required' => true))
                ->add('subcategory', 'entity',
                        array(
                            'class' => 'CategoryBundle:Subcategory',
                            'query_builder' => function (EntityRepository $er) {
                                    return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                                }, 'expanded' => false,
                            'required' => true
                        )
                    )
                /*
                ->add('stock', 'number', array('required' => false))*/
                ->add('package', 'entity',
                    array(
                        'class' => 'ItemBundle:ItemPackage',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('it')->orderBy('it.name', 'ASC');
                        }, 'expanded' => false,
                        'required' => true
                    )
                )
                ->add('tax', 'entity',
                    array(
                        'class' => 'ItemBundle:Tax',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('t')->orderBy('t.name', 'ASC');
                        }, 'expanded' => false,
                        'required' => true
                    )
                );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Ecommerce\ItemBundle\Entity\Item',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'item';
    }
}