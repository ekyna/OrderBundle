<?php

namespace Ekyna\Bundle\OrderBundle\Helper;

use Ekyna\Component\Sale\Order\OrderInterface;

/**
 * Interface OrderHelperInterface
 * @package Ekyna\Bundle\OrderBundle\Helper
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface OrderHelperInterface
{
    /**
     * Returns whether the order contains the subject or not.
     *
     * @param OrderInterface $order
     * @param object         $subject
     * @return bool
     * @throws \Ekyna\Bundle\OrderBundle\Exception\OrderException
     */
    public function hasSubject(OrderInterface $order, $subject);

    /**
     * Adds the subject to the order.
     *
     * @param OrderInterface $order
     * @param object         $subject
     * @param integer        $quantity
     * @return \Ekyna\Bundle\OrderBundle\Event\OrderItemEvent
     * @throws \Ekyna\Bundle\OrderBundle\Exception\OrderException
     */
    public function addSubject(OrderInterface $order, $subject, $quantity = 1);

    /**
     * Removes the subject from the order.
     *
     * @param OrderInterface $order
     * @param object         $subject
     * @return \Ekyna\Bundle\OrderBundle\Event\OrderItemEvent
     * @throws \Ekyna\Bundle\OrderBundle\Exception\OrderException
     */
    public function removeSubject(OrderInterface $order, $subject);
}
