<?php

namespace Ekyna\Bundle\OrderBundle\Service;

use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;
use Ekyna\Component\Sale\Payment\PaymentStates;
use Ekyna\Component\Sale\Tax\TaxAmount;
use Ekyna\Component\Sale\Tax\TaxesAmounts;

/**
 * Class Calculator
 * @package Ekyna\Bundle\OrderBundle\Service
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Calculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculateOrderItemTotal(OrderItemInterface $item, $ati = false)
    {
        $total = $item->getPrice() * $item->getQuantity();
        if ($ati && null !== $tax = $item->getTax()) {
            $total *= 1 + $tax->getRate();
        }
        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderItemsTotal(OrderInterface $order, $ati = false)
    {
        $total = 0;
        foreach ($order->getItems() as $item) {
            $total += $this->calculateOrderItemTotal($item, $ati);
        }
        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderShipmentTotal(OrderInterface $order, $ati = false)
    {
        return 0; /* TODO */
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderTotal(OrderInterface $order, $ati = false)
    {
        return $this->calculateOrderItemsTotal($order, $ati)
             + $this->calculateOrderShipmentTotal($order, $ati);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderItemTaxAmount(OrderItemInterface $item)
    {
        if (null !== $tax = $item->getTax()) {
            return new TaxAmount(
                $item->getTax(),
                $this->calculateOrderItemTotal($item, true) - $this->calculateOrderItemTotal($item)
            );
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderTaxesAmounts(OrderInterface $order)
    {
        $taxesAmounts = new TaxesAmounts();
        foreach ($order->getItems() as $item) {
            if (null !== $taxAmount = $this->calculateOrderItemTaxAmount($item)) {
                $taxesAmounts->addTaxAmount($taxAmount);
            }
        }
        return $taxesAmounts;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderItemTotalWeight(OrderItemInterface $item)
    {
        return $item->getWeight() * $item->getQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderTotalWeight(OrderInterface $order)
    {
        $total = 0;
        foreach ($order->getItems() as $item) {
            $total += $this->calculateOrderItemTotalWeight($item);
        }
        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderItemsCount(OrderInterface $order)
    {
        $count = 0;
        foreach ($order->getItems() as $item) {
            $count += $item->getQuantity();
        }
        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderPaidTotal(OrderInterface $order)
    {
        $total = 0;
        foreach ($order->getPayments() as $payment) {
            if (in_array($payment->getState(), array(PaymentStates::STATE_AUTHORIZED, PaymentStates::STATE_COMPLETED))) {
                $total += $payment->getAmount();
            }
        }
        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrderRemainingTotal(OrderInterface $order)
    {
        $amount = $this->calculateOrderTotal($order, true) - $this->calculateOrderPaidTotal($order);
        if (0 > $amount) {
            $amount = 0;
        }
        return $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTotals(OrderInterface $order)
    {
        // TODO: Adjustments / Shipping
        $order
            ->setItemsCount($this->calculateOrderItemsCount($order))
            ->setTotalWeight($this->calculateOrderTotalWeight($order))
            ->setNetTotal($this->calculateOrderTotal($order))
            ->setAtiTotal($this->calculateOrderTotal($order, true))
        ;
    }
}
