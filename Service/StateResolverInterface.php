<?php

namespace Ekyna\Bundle\OrderBundle\Service;

use Ekyna\Bundle\AdminBundle\Event\ResourceEvent;
use Ekyna\Component\Sale\Order\OrderInterface;

/**
 * Interface StateResolverInterface
 * @package Ekyna\Bundle\OrderBundle\Service
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface StateResolverInterface
{
    /**
     * Resolves the global order state.
     *
     * @param OrderInterface $order
     * @param ResourceEvent $event
     *
     * @return string
     */
    public function resolve(OrderInterface $order, ResourceEvent $event = null);
}
