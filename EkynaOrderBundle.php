<?php

namespace Ekyna\Bundle\OrderBundle;

use Ekyna\Bundle\CoreBundle\AbstractBundle;
use Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler\AdminMenuPass;
use Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler\ItemProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EkynaOrderBundle
 * @package Ekyna\Bundle\OrderBundle
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaOrderBundle extends AbstractBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AdminMenuPass());
        $container->addCompilerPass(new ItemProviderPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return [
            'Ekyna\Component\Sale\Order\OrderInterface' => 'ekyna_order.order.class',
        ];
    }
}
