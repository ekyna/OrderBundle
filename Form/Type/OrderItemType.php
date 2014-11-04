<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class OrderItemType
 * @package Ekyna\Bundle\OrderBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('designation', 'text', array(
                'label' => 'ekyna_core.field.designation',
                'sizing' => 'sm',
                'attr' => array(
                    'label_col' => 4,
                    'widget_col' => 8
                )
            ))
            ->add('reference', 'text', array(
                'label' => 'ekyna_core.field.reference',
                'sizing' => 'sm',
                'attr' => array(
                    'label_col' => 4,
                    'widget_col' => 8
                )
            ))
            ->add('product', 'entity', array(
                'label' => 'Produit',
                'class' => 'Ekyna\Bundle\ProductBundle\Entity\AbstractProduct',
                'multiple' => false,
                'required' => false,
                'property' => 'designation',
                'empty_value' => 'Produit',
                'sizing' => 'sm',
                'attr' => array(
                    'placeholder' => 'Produit',
                    'label_col' => 4,
                    'widget_col' => 8
                ),
            ))
            ->add('weight', 'integer', array(
                'label' => 'ekyna_core.field.weight',
                'sizing' => 'sm',
                'attr' => array(
                    'input_group' => array('append' => 'g'),
                    'min' => 0,
                    'label_col' => 4,
                    'widget_col' => 8
                ),
            ))
            ->add('price', 'number', array(
                'label' => 'ekyna_core.field.price',
                'precision' => 5,
                'sizing' => 'sm',
                'attr' => array(
                    'input_group' => array('append' => '€'),
                    'label_col' => 4,
                    'widget_col' => 8
                ),
            ))
            ->add('quantity', 'integer', array(
                'label' => 'ekyna_core.field.quantity',
                'sizing' => 'sm',
                'attr' => array(
                    'label_col' => 4,
                    'widget_col' => 8
                ),
            ))
            ->add('tax', 'ekyna_resource', array(
                'label' => 'ekyna_core.field.tax',
                'class' => 'Ekyna\Bundle\ProductBundle\Entity\Tax',
                'multiple' => false,
                'property' => 'name',
                'empty_value' => 'ekyna_core.field.tax',
                'allow_new' => $options['admin_mode'],
                'sizing' => 'sm',
                'attr' => array(
                    'placeholder' => 'ekyna_core.field.tax',
                    'label_col' => 4,
                    'widget_col' => 8
                ),
            ))
            ->add('position', 'hidden', array('attr' => array('data-role' => 'position')))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_order_order_item';
    }
}
