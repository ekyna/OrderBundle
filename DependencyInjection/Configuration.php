<?php

namespace Ekyna\Bundle\OrderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Configuration
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ekyna_order');

        $this->addPoolsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `pools` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addPoolsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('pools')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('order')
                            ->isRequired()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('templates')->defaultValue('EkynaOrderBundle:Order/Admin')->end()
                                ->scalarNode('parent')->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\OrderBundle\Entity\Order')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\OrderBundle\Controller\Admin\OrderController')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\OrderBundle\Entity\OrderRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\OrderBundle\Form\Type\OrderType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\OrderBundle\Table\Type\OrderType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
