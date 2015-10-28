<?php

namespace Ekyna\Bundle\OrderBundle;

use Ekyna\Bundle\CoreBundle\AbstractBundle;
use Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler as Pass;
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

        $container->addCompilerPass(new Pass\AdminMenuPass());
        $container->addCompilerPass(new Pass\ItemProviderPass());
        $container->addCompilerPass(new Pass\RepositoryPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Ekyna\Component\Sale\Order\OrderInterface' => 'ekyna_order.order.class',
        );
    }
}
