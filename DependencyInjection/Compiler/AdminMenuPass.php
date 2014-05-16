<?php

namespace Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * AdminMenuPass
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AdminMenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_admin.menu.pool')) {
            return;
        }

        $pool = $container->getDefinition('ekyna_admin.menu.pool');

        $pool->addMethodCall('createGroup', array(array(
            'name'     => 'orde',
            'label'    => 'ekyna_order.order.label.plural',
            'icon'     => 'shopping-cart',
            'position' => 5,
        )));
        $pool->addMethodCall('createEntry', array('orde', array(
            'name'     => 'orders',
            'route'    => 'ekyna_order_order_admin_home',
            'label'    => 'ekyna_order.order.label.plural',
            'resource' => 'ekyna_order_order',
        )));
    }
}
