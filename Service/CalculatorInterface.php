<?php

namespace Ekyna\Bundle\OrderBundle\Service;

use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;
use Ekyna\Component\Sale\Tax\TaxAmount;
use Ekyna\Component\Sale\Tax\TaxesAmounts;

/**
 * Interface CalculatorInterface
 * @package Ekyna\Bundle\OrderBundle\Service
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface CalculatorInterface
{
    /**
     * Calculates the order item total.
     *
     * @param OrderItemInterface $item The order item
     * @param bool               $ati  Whether to include the taxes or not.
     * @return float
     */
    public function calculateOrderItemTotal(OrderItemInterface $item, $ati = false);

    /**
     * Calculates the order (all) items total.
     *
     * @param OrderInterface $order The order
     * @param bool           $ati   Whether to include the taxes or not.
     * @return float
     */
    public function calculateOrderItemsTotal(OrderInterface $order, $ati = false);

    /**
     * Calculates the order shipment total.
     *
     * @param OrderInterface $order The order
     * @param bool           $ati   Whether to include the taxes or not.
     * @return float
     */
    public function calculateOrderShipmentTotal(OrderInterface $order, $ati = false);

    /**
     * Calculates the order total.
     *
     * @param OrderInterface $order The order
     * @param bool           $ati   Whether to include the taxes or not.
     * @return float
     */
    public function calculateOrderTotal(OrderInterface $order, $ati = false);

    /**
     * Calculates the order item tax amount.
     *
     * @param OrderItemInterface $item The order item
     * @return TaxAmount|null
     */
    public function calculateOrderItemTaxAmount(OrderItemInterface $item);

    /**
     * Calculates the order taxes amounts.
     *
     * @param OrderInterface $order The order
     * @return TaxesAmounts
     */
    public function calculateOrderTaxesAmounts(OrderInterface $order);

    /**
     * Calculates the order item total weight.
     *
     * @param OrderItemInterface $item The order item
     * @return float
     */
    public function calculateOrderItemTotalWeight(OrderItemInterface $item);

    /**
     * Calculates the order total weight.
     *
     * @param OrderInterface $order The order
     * @return float
     */
    public function calculateOrderTotalWeight(OrderInterface $order);

    /**
     * Calculates the order items count.
     *
     * @param OrderInterface $order The order
     * @return integer
     */
    public function calculateOrderItemsCount(OrderInterface $order);

    /**
     * Calculates the order paid total.
     *
     * @param OrderInterface $order
     * @return float
     */
    public function calculateOrderPaidTotal(OrderInterface $order);

    /**
     * Calculates the order remaining total.
     *
     * @param OrderInterface $order
     * @return float
     */
    public function calculateOrderRemainingTotal(OrderInterface $order);

    /**
     * Updates the order totals.
     *
     * @param \Ekyna\Component\Sale\Order\OrderInterface $order
     */
    public function updateTotals(OrderInterface $order);
}
