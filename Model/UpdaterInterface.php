<?php

namespace Ekyna\Bundle\OrderBundle\Model;

use Ekyna\Component\Sale\Order\OrderInterface;

/**
 * UpdaterInterface
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface UpdaterInterface
{
    /**
     * Updates the order totals
     *
     * @param \Ekyna\Component\Sale\Order\OrderInterface $order
     */
    public function update(OrderInterface $order);
}
