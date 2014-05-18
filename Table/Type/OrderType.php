<?php

namespace Ekyna\Bundle\OrderBundle\Table\Type;

use Doctrine\ORM\QueryBuilder;
use Ekyna\Component\Table\TableBuilderInterface;
use Ekyna\Component\Table\AbstractTableType;
use Ekyna\Component\Sale\Order\OrderInterface;

/**
 * OrderType
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderType extends AbstractTableType
{
    protected $entityClass;

    public function __construct($class)
    {
        $this->entityClass = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilderInterface $tableBuilder)
    {
        $tableBuilder
            ->addColumn('number', 'anchor', array(
                'label' => 'ekyna_core.field.number',
                'route_name' => 'ekyna_order_order_admin_show',
                'route_parameters_map' => array(
                    'orderId' => 'id'
                ),
            ))
            ->addColumn('updatedAt', 'datetime', array(
                'label' => 'ekyna_core.field.update_date',
            ))
            ->addColumn('actions', 'admin_actions', array(
                'buttons' => array(
                    array(
                        'label' => 'ekyna_core.button.edit',
                        'icon' => 'pencil',
                        'class' => 'warning',
                        'route_name' => 'ekyna_order_order_admin_edit',
                        'route_parameters_map' => array(
                            'orderId' => 'id'
                        ),
                        'permission' => 'edit',
                    ),
                    array(
                        'label' => 'ekyna_core.button.remove',
                        'icon' => 'trash',
                        'class' => 'danger',
                        'route_name' => 'ekyna_order_order_admin_remove',
                        'route_parameters_map' => array(
                            'orderId' => 'id'
                        ),
                        'permission' => 'delete',
                    ),
                ),
            ))
            ->setDefaultSort('number')
            ->setCustomizeQueryBuilder(function(QueryBuilder $qb) {
                $qb
                    ->andWhere($qb->expr()->eq('a.type', ':type'))
                    ->setParameter('type', OrderInterface::TYPE_ORDER)
                ;
            });
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_order_order';
    }
}
