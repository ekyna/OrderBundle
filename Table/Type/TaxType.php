<?php

namespace Ekyna\Bundle\OrderBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Component\Table\TableBuilderInterface;

/**
 * Class TaxType
 * @package Ekyna\Bundle\OrderBundle\Table\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TaxType extends ResourceTableType
{
    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->addColumn('id', 'number', [
                'sortable' => true,
            ])
            ->addColumn('name', 'anchor', [
                'label' => 'ekyna_core.field.name',
                'sortable' => true,
                'route_name' => 'ekyna_product_tax_admin_show',
                'route_parameters_map' => [
                    'taxId' => 'id'
                ],
            ])
            ->addColumn('rate', 'number', [
                'label' => 'ekyna_core.field.rate',
                'sortable' => true,
            ])
            ->addColumn('actions', 'admin_actions', [
                'buttons' => [
                    [
                        'label' => 'ekyna_core.button.edit',
                        'class' => 'warning',
                        'route_name' => 'ekyna_order_tax_admin_edit',
                        'route_parameters_map' => [
                            'taxId' => 'id'
                        ],
                        'permission' => 'edit',
                    ],
                    [
                        'label' => 'ekyna_core.button.remove',
                        'class' => 'danger',
                        'route_name' => 'ekyna_order_tax_admin_remove',
                        'route_parameters_map' => [
                            'taxId' => 'id'
                        ],
                        'permission' => 'delete',
                    ],
                ],
            ])
            ->addFilter('id', 'number')
            ->addFilter('name', 'text', [
            	'label' => 'ekyna_core.field.name'
            ])
            ->addFilter('rate', 'number', [
            	'label' => 'ekyna_core.field.rate'
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_order_tax';
    }
}
