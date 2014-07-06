<?php

namespace Ekyna\Bundle\OrderBundle\Event;

/**
 * OrderEvents.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
final class OrderEvents
{
    const ITEM_ADD           = 'ekyna_order.order.event.item_add';
    const ITEM_REMOVE        = 'ekyna_order.order.event.item_remove';

    const STATE_CHANGE       = 'ekyna_order.order.event.state_change';
    const CONTENT_CHANGE     = 'ekyna_order.order.event.content_change';

    const UPDATE             = 'ekyna_order.order.event.update';
    const DELETE             = 'ekyna_order.order.event.delete';

    const PAYMENT_INITIALIZE = 'ekyna_order.order.event.payment_initialize';
    const PAYMENT_COMPLETE   = 'ekyna_order.order.event.payment_complete';
}
