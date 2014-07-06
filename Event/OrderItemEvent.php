<?php

namespace Ekyna\Bundle\OrderBundle\Event;

use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;

/**
 * OrderItemEvent.
 *
 * @author Etienne Dauvergne <contact@ekyna.com>
 */
class OrderItemEvent extends OrderEvent
{
    /**
     * @var OrderItemInterface
     */
    protected $item;

    /**
     * Constructor.
     * 
     * @param OrderInterface $order
     * @param OrderItemInterface $item
     */
    public function __construct(OrderInterface $order, OrderItemInterface $item)
    {
        parent::__construct($order);

        $this->item = $item;
    }

    /**
     * Returns the item.
     * 
     * @return OrderItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }
}
