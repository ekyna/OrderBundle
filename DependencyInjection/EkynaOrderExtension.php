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
        $config = $this->configure($configs, 'ekyna_order', new Configuration(), $container);

        $container->setParameter('ekyna_order.document_logo', $config['document_logo']);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        $container->prependExtensionConfig('ekyna_user', array(
            'account' => array(
                'enable'    => true,
                'profile'   => true,
                'register'  => true,
                'resetting' => true,
                'address'   => true,
            ),
        ));

        if (true === isset($bundles['AsseticBundle'])) {
            $container->prependExtensionConfig('assetic', array(
                'bundles' => array('EkynaOrderBundle')
            ));
        }
    }
}
