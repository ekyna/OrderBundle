<?php

namespace Ekyna\Bundle\OrderBundle\Event;

use Ekyna\Component\Sale\Order\OrderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * OrderEvent.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderEvent extends Event
{
    /**
     * @var \Ekyna\Component\Sale\Order\OrderInterface
     */
    protected $order;

    /**
     * Constructor.
     * 
     * @param \Ekyna\Component\Sale\Order\OrderInterface $order
     */
    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * Returns the order.
     * 
     * @return \Ekyna\Component\Sale\Order\OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }
}
