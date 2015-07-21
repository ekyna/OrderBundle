<?php

namespace Ekyna\Bundle\OrderBundle\Event;

/**
 * Class OrderEvents
 * @package Ekyna\Bundle\OrderBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
final class OrderEvents
{
    const STATE_CHANGE       = 'ekyna_order.order.state_change';
    const CONTENT_CHANGE     = 'ekyna_order.order.content_change';

    const PRE_CREATE         = 'ekyna_order.order.pre_create';
    const POST_CREATE        = 'ekyna_order.order.post_create';

    const PRE_UPDATE         = 'ekyna_order.order.pre_update';
    const POST_UPDATE        = 'ekyna_order.order.post_update';

    const PRE_DELETE         = 'ekyna_order.order.pre_delete';
    const POST_DELETE        = 'ekyna_order.order.post_delete';
}
