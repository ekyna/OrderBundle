<?php

namespace Ekyna\Bundle\OrderBundle;

use Ekyna\Bundle\OrderBundle\DependencyInjection\Compiler\AdminMenuPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * EkynaOrderBundle
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaOrderBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AdminMenuPass());
    }
}
