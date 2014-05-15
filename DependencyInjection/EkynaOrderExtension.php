<?php

namespace Ekyna\Bundle\OrderBundle\DependencyInjection;

use Ekyna\Bundle\AdminBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * EkynaOrderExtension
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaOrderExtension extends AbstractExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        list($config, $loader) = $this->configure($configs, 'ekyna_order', new Configuration(), $container);

        $container->setParameter('ekyna_order.document_logo', $config['document_logo']);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $config = array(
            'bundles' => array('EkynaOrderBundle')
        );
        if (true === isset($bundles['AsseticBundle'])) {
            $this->configureAsseticBundle($container, $config);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @return void
     */
    protected function configureAsseticBundle(ContainerBuilder $container, array $config)
    {
        foreach (array_keys($container->getExtensions()) as $name) {
            if ($name == 'assetic') {
                $container->prependExtensionConfig($name, $config);
                break;
            }
        }
    }
}
