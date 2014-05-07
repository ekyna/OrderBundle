<?php

namespace Ekyna\Bundle\OrderBundle\Model;

use Ekyna\Component\Sale\Order\OrderInterface;

/**
 * StateResolverInterface.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface StateResolverInterface
{
    /**
     * Resolves the global order state.
     *
     * @param OrderInterface $order
     *
     * @return string
     */
    public function resolve(OrderInterface $order);
}
