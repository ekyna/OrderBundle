<?php

namespace Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RepositoryPass
 * @package Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class RepositoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_order.order.repository')) {
            return;
        }
        if (!$container->hasDefinition('ekyna_order.item_helper')) {
            return;
        }

        $container
            ->getDefinition('ekyna_order.order.repository')
            ->addMethodCall('setItemHelper', array(
                new Reference('ekyna_order.item_helper')
            ))
        ;
    }
}
