<?php

namespace Ekyna\Bundle\OrderBundle\Event;

/**
 * Class OrderItemEvents
 * @package Ekyna\Bundle\OrderBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemEvents
{
    const ADD    = 'ekyna_order.order_item.add';
    const SYNC   = 'ekyna_order.order_item.sync';
    const REMOVE = 'ekyna_order.order_item.remove';
}
