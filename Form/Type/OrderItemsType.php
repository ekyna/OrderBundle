<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrderItemsType
 * @package Ekyna\Bundle\OrderBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'label'           => false,
                'type'            => 'ekyna_order_order_item',
                'allow_add'       => true,
                'allow_delete'    => true,
                'allow_sort'      => true,
                'add_button_text' => 'ekyna_core.button.add',
                'sub_widget_col'  => 11,
                'button_col'      => 1,
                'attr'            => array(
                    'widget_col' => 12
                ),
                'options'         => array(
                    'label'    => false,
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ekyna_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_order_order_items';
    }
}
