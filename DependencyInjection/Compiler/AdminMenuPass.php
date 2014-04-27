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

        $pool->addMethodCall('createGroupReference', array(
            'orde', 'ekyna_order.order.label.plural', 'shopping-cart', null, 10
        ));
        $pool->addMethodCall('createEntryReference', array(
            'orde', 'orders', 'ekyna_order_order_admin_home', 'ekyna_order.order.label.plural'
        ));
    }
}
