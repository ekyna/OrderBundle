<?php

namespace Ekyna\Bundle\OrderBundle\Event;

/**
 * OrderEvents.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
final class OrderEvents
{
    const PRE_CONTENT_CHANGE   = 'ekyna_order.pre_content_change';
    const POST_CONTENT_CHANGE  = 'ekyna_order.post_content_change';

    const PRE_PAYMENT_PROCESS  = 'ekyna_order.pre_payment_process';
    const POST_PAYMENT_PROCESS = 'ekyna_order.post_payment_process';

    const PRE_STATE_CHANGE     = 'ekyna_order.pre_state_change';
    const POST_STATE_CHANGE    = 'ekyna_order.post_state_change';
}
