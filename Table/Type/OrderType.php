<?php

namespace Ekyna\Bundle\OrderBundle\Table\Type;

use Doctrine\ORM\QueryBuilder;
use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Bundle\OrderBundle\Model\OrderStates;
use Ekyna\Bundle\PaymentBundle\Model\PaymentStates;
use Ekyna\Bundle\ShipmentBundle\Model\ShipmentStates;
use Ekyna\Component\Sale\Order\OrderTypes;
use Ekyna\Component\Table\TableBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OrderType
 * @package Ekyna\Bundle\OrderBundle\Table\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderType extends ResourceTableType
{
    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->addColumn('number', 'anchor', [
                'label' => 'ekyna_core.field.number',
                'route_name' => 'ekyna_order_order_admin_show',
                'route_parameters_map' => [
                    'orderId' => 'id'
                ],
            ])
            ->addColumn('email', 'anchor', [
                'label' => 'ekyna_core.field.email',
                'sortable' => true,
                'route_name' => 'ekyna_user_user_admin_show',
                'route_parameters_map' => ['userId' => 'user.id'], // TODO check if no user ?
            ])
            ->addColumn('firstName', 'text', [
                'label' => 'ekyna_core.field.first_name',
                'sortable' => true,
            ])
            ->addColumn('lastName', 'text', [
                'label' => 'ekyna_core.field.last_name',
                'sortable' => true,
            ])
            ->addColumn('atiTotal', 'number', [
                'label' => 'ekyna_order.order.field.ati_total',
                'sortable' => true,
            ])
            ->addColumn('state', 'choice', [
                'label' => 'ekyna_order.order.field.state',
                'choices' => OrderStates::getChoices(),
            ])
            ->addColumn('paymentState', 'choice', [
                'label' => 'ekyna_order.order.field.payment_state',
                'choices' => PaymentStates::getChoices(),
            ])
            ->addColumn('shipmentState', 'choice', [
                'label' => 'ekyna_order.order.field.shipment_state',
                'choices' => ShipmentStates::getChoices(),
            ])
            ->addColumn('actions', 'admin_actions', [
                'buttons' => [
                    [
                        'label' => 'ekyna_core.button.edit',
                        'class' => 'warning',
                        'route_name' => 'ekyna_order_order_admin_edit',
                        'route_parameters_map' => [
                            'orderId' => 'id'
                        ],
                        'permission' => 'edit',
                    ],
                    [
                        'label' => 'ekyna_core.button.remove',
                        'class' => 'danger',
                        'route_name' => 'ekyna_order_order_admin_remove',
                        'route_parameters_map' => [
                            'orderId' => 'id'
                        ],
                        'permission' => 'delete',
                    ],
                ],
            ])
            ->addFilter('number', 'text', [
                'label' => 'ekyna_core.field.number',
            ])
            ->addFilter('email', 'text', [
                'label' => 'ekyna_core.field.email',
            ])
            ->addFilter('firstName', 'text', [
                'label' => 'ekyna_core.field.first_name',
            ])
            ->addFilter('lastName', 'text', [
                'label' => 'ekyna_core.field.last_name',
            ])
            ->addFilter('atiTotal', 'number', [
                'label' => 'ekyna_order.order.field.ati_total',
            ])
            ->addFilter('state', 'choice', [
                'label' => 'ekyna_order.order.field.state',
                'choices' => OrderStates::getChoices(),
            ])
            ->addFilter('paymentState', 'choice', [
                'label' => 'ekyna_order.order.field.payment_state',
                'choices' => PaymentStates::getChoices(),
            ])
            ->addFilter('shipmentState', 'choice', [
                'label' => 'ekyna_order.order.field.shipment_state',
                'choices' => ShipmentStates::getChoices(),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'default_sorts' => ['number desc'],
            'customize_qb' => function(QueryBuilder $qb, $alias) {
                $qb
                    ->andWhere($qb->expr()->eq($alias.'.type', ':type'))
                    ->setParameter('type', OrderTypes::TYPE_ORDER)
                    ->andWhere($qb->expr()->isNull($alias.'.deletedAt'))
                ;
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_order_order';
    }
}
