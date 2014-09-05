<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * OrderType.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', 'text', array(
                'label' => 'ekyna_core.field.number',
                'required' => true,
                'disabled' => true,
            ))
            ->add('user', 'ekyna_core_entity_search', array(
                'label' => 'ekyna_core.field.user',
                'required' => true,
                'entity'   => 'Ekyna\Bundle\UserBundle\Entity\User',
                'search_route' => 'ekyna_user_user_admin_search',
                'find_route'   => 'ekyna_user_user_admin_find',
                'allow_clear'  => false,
            ))
            ->add('items', 'collection', array(
                'label'        => false,
                'type'         => 'ekyna_order_order_item',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => array(
                    'widget_col' => 12
                ),
                'options'      => array(
                    'label' => false,
                    'required' => false,
                    'attr' => array(
                        'widget_col' => 12
                    ),
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_order_order';
    }
}
