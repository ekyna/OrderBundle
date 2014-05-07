<?php

namespace Ekyna\Bundle\OrderBundle\Resolver;

use Ekyna\Bundle\OrderBundle\Model\StateResolverInterface;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderStates;
use Ekyna\Component\Sale\Payment\PaymentStates;
use Ekyna\Component\Sale\Shipment\ShipmentStates;

class OrderStateResolver implements StateResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        $orderState = $order->getState();

        $paymentState = $this->resolvePaymentsState($order);
        $shipmentState = $this->resolveShipmentsState($order);

        if (in_array($paymentState, array(PaymentStates::STATE_SUCCESS, PaymentStates::STATE_COMPLETED))) {
            $orderState = OrderStates::STATE_ACCEPTED;
            if ($shipmentState == ShipmentStates::STATE_SHIPPED) {
                $orderState = OrderStates::STATE_COMPLETED;
            }
        } elseif ($paymentState == PaymentStates::STATE_FAILED) {
            $orderState = OrderStates::STATE_REFUSED;
        } elseif (in_array($paymentState, array(PaymentStates::STATE_CANCELLED, PaymentStates::STATE_REFUNDED))) {
            $orderState = OrderStates::STATE_CANCELLED;
        } else {
            $orderState = OrderStates::STATE_PENDING;
        }

        $order
            ->setState($orderState)
            ->setPaymentState($paymentState)
            ->setShipmentState($shipmentState)
        ;
    }

    /**
     * Resolves the global payment state.
     * 
     * @param OrderInterface $order
     * 
     * @return string
     */
    protected function resolvePaymentsState(OrderInterface $order)
    {
        $allCount = 0;
        $statesCounts = array();
        $totalPaid = 0;

        foreach ($order->getPayments() as $payment) {
            if (in_array($payment->getState(), array(PaymentStates::STATE_SUCCESS, PaymentStates::STATE_COMPLETED))) {
                $totalPaid += $payment->getAmount();
            }
            if (array_key_exists($payment->getState(), $statesCounts)) {
                $statesCounts[$payment->getState()]++;
            } else {
                $statesCounts[$payment->getState()] = 1;
            }
            $allCount++;
        }

        if ($totalPaid >= $order->getAtiTotal()) {
            if (array_key_exists(PaymentStates::STATE_COMPLETED, $statesCounts) 
                && $statesCounts[PaymentStates::STATE_COMPLETED] == $allCount) {
                return PaymentStates::STATE_COMPLETED;
            }
            return PaymentStates::STATE_SUCCESS;
        }

        foreach ($statesCounts as $state => $count) {
            if ($count == $allCount) {
                return $state;
            }
        }

        return PaymentStates::STATE_PENDING;
    }

    /**
     * Resolves the global shipment state.
     * 
     * @param OrderInterface $order
     * 
     * @return string
     */
    protected function resolveShipmentsState(OrderInterface $order)
    {
        if (! $order->requiresShipment()) {
            return ShipmentStates::STATE_SHIPPED;
        }

        return ShipmentStates::STATE_PENDING;
    }
}
