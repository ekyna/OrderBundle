<?php

namespace Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class AdminMenuPass
 * @package Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AdminMenuPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_admin.menu.pool')) {
            return;
        }

        $pool = $container->getDefinition('ekyna_admin.menu.pool');

        $pool->addMethodCall('createGroup', array(array(
            'name'     => 'order',
            'label'    => 'ekyna_order.order.label.plural',
            'icon'     => 'shopping-cart',
            'position' => 10,
        )));
        $pool->addMethodCall('createEntry', array('order', array(
            'name'     => 'orders',
            'route'    => 'ekyna_order_order_admin_home',
            'label'    => 'ekyna_order.order.label.plural',
            'resource' => 'ekyna_order_order',
        )));
        $pool->addMethodCall('createEntry', array('order', array(
            'name'     => 'taxes',
            'route'    => 'ekyna_order_tax_admin_home',
            'label'    => 'ekyna_order.tax.label.plural',
            'resource' => 'ekyna_order_tax',
            'position' => 99,
        )));
    }
}
