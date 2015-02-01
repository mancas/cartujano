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
                ->add('reference', 'number', array('required' => true))
                ->add('description', 'textarea', array('required' => true))
                ->add('price', 'number', array('required' => true))
                ->add('weight', 'number', array('required' => false))
                ->add('subcategory', 'entity',
                        array(
                            'class' => 'CategoryBundle:Subcategory',
                            'query_builder' => function (EntityRepository $er) {
                                    return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                                }, 'expanded' => false,
                            'required' => true
                        )
                    )
                ->add('manufacturers', 'entity',
                    array(
                        'class' => 'ItemBundle:Manufacturer',
                        'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('m')->orderBy('m.name', 'ASC');
                            }, 'expanded' => false,
                        'multiple' => true,
                        'required' => false
                    )
                )
                ->add('stock', 'number', array('required' => false))
                ->add('type', 'entity',
                    array(
                        'class' => 'ItemBundle:ItemType',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('it')->orderBy('it.name', 'ASC');
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