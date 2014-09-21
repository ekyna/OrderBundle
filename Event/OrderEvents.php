<?php

namespace Ekyna\Bundle\OrderBundle\Event;

/**
 * OrderEvents.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
final class OrderEvents
{
    const ITEM_ADD           = 'ekyna_order.order.item_add';
    const ITEM_REMOVE        = 'ekyna_order.order.item_remove';

    const STATE_CHANGE       = 'ekyna_order.order.state_change';
    const CONTENT_CHANGE     = 'ekyna_order.order.content_change';

    const UPDATE             = 'ekyna_order.order.update';
    const DELETE             = 'ekyna_order.order.delete';

    const PAYMENT_INITIALIZE = 'ekyna_order.order.payment_initialize';
    const PAYMENT_COMPLETE   = 'ekyna_order.order.payment_complete';
}
