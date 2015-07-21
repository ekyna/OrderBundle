<?php

namespace Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ItemProviderPass
 * @package Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ItemProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_order.item_provider_registry')) {
            return;
        }

        $registry = $container->getDefinition('ekyna_order.item_provider_registry');

        $providers = $container->findTaggedServiceIds('ekyna_order.item_provider');
        foreach ($providers as $id => $attributes) {
            $registry->addMethodCall('addProvider', array(new Reference($id)));
        }
    }
}
