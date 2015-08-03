<?php

namespace Ekyna\Bundle\OrderBundle\Service;

use Ekyna\Bundle\AdminBundle\Event\ResourceEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvents;
use Ekyna\Bundle\OrderBundle\Exception\LogicException;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderStates;
use Ekyna\Component\Sale\Payment\PaymentStates;
use Ekyna\Component\Sale\Shipment\ShipmentStates;
use SM\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class StateResolver
 * @package Ekyna\Bundle\OrderBundle\Service
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class StateResolver implements StateResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;


    /**
     * Constructor.
     *
     * @param FactoryInterface         $factory
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $dispatcher)
    {
        $this->factory    = $factory;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order, ResourceEvent $event = null)
    {
        $oldState = $order->getState();

        if ($order->isEmpty()) {
            // TODO back to STATE_NEW ?
            return;
        }

        $paymentState = $this->resolvePaymentsState($order);
        $shipmentState = $this->resolveShipmentsState($order);

        if (in_array($paymentState, array(PaymentStates::STATE_PENDING, PaymentStates::STATE_AUTHORIZED, PaymentStates::STATE_COMPLETED))) {
            $newState = OrderStates::STATE_ACCEPTED;
            if ($paymentState === PaymentStates::STATE_COMPLETED && $shipmentState == ShipmentStates::STATE_SHIPPED) {
                $newState = OrderStates::STATE_COMPLETED;
            }
        } elseif ($paymentState == PaymentStates::STATE_FAILED) {
            $newState = OrderStates::STATE_REFUSED;
        } elseif (in_array($paymentState, array(PaymentStates::STATE_CANCELLED, PaymentStates::STATE_REFUNDED))) {
            $newState = OrderStates::STATE_CANCELLED;
        } else {
            $newState = OrderStates::STATE_PENDING;
        }

        $order
            ->setPaymentState($paymentState)
            ->setShipmentState($shipmentState)
        ;

        if ($newState != $oldState) {
            /** @var \Ekyna\Bundle\OrderBundle\Service\StateMachineInterface $sm */
            $sm = $this->factory->get($order);
            if (null === $transition = $sm->getTransitionToState($newState)) {
                throw new LogicException(sprintf('No transition found from state "%s" to state "%s".', $oldState, $newState));
            } else {
                $sm->apply($transition);

                $orderEvent = $event instanceof OrderEvent ? $event : new OrderEvent($order);
                $this->dispatcher->dispatch(OrderEvents::STATE_CHANGE, $orderEvent);
                if (!$event instanceof OrderEvent) {
                    $event->addMessages($orderEvent->getMessages());
                }
            }
        }
    }

    /**
     * Resolves the global payment state.
     * 
     * @param OrderInterface $order
     * @return string
     */
    private function resolvePaymentsState(OrderInterface $order)
    {
        $completedTotal = $authorizedTotal = $refundTotal = $failedTotal = 0;

        $payments = $order->getPayments();
        if (0 < $payments->count()) {
            // Gather state amounts
            foreach ($payments as $payment) {
                if ($payment->getState() == PaymentStates::STATE_COMPLETED) {
                    $completedTotal += $payment->getAmount();
                } else if ($payment->getState() == PaymentStates::STATE_AUTHORIZED) {
                    $authorizedTotal += $payment->getAmount();
                } else if ($payment->getState() == PaymentStates::STATE_REFUNDED) {
                    $refundTotal += $payment->getAmount();
                } else if ($payment->getState() == PaymentStates::STATE_FAILED) {
                    $failedTotal += $payment->getAmount();
                }
            }

            // State by amounts
            if ($completedTotal >= $order->getAtiTotal()) {
                return PaymentStates::STATE_COMPLETED;
            } elseif ($authorizedTotal + $completedTotal >= $order->getAtiTotal()) {
                return PaymentStates::STATE_AUTHORIZED;
            } elseif ($refundTotal >= $order->getAtiTotal()) {
                return PaymentStates::STATE_REFUNDED;
            } elseif ($failedTotal >= $order->getAtiTotal()) {
                return PaymentStates::STATE_FAILED;
            }

            // Check for offline pending payment
            foreach ($payments as $payment) {
                if (in_array($payment->getState(), array(PaymentStates::STATE_PENDING, PaymentStates::STATE_PROCESSING))
                    && $payment->getMethod()->getFactoryName() === 'offline') {
                    return PaymentStates::STATE_PENDING;
                }
            }
        }

        return PaymentStates::STATE_NEW;
    }

    /**
     * Resolves the global shipment state.
     * 
     * @param OrderInterface $order
     * @return string
     */
    private function resolveShipmentsState(OrderInterface $order)
    {
        if (!$order->isEmpty()) {
            if (! $order->requiresShipment()) {
                return ShipmentStates::STATE_SHIPPED;
            }

            // TODO Shipments states
        }

        return ShipmentStates::STATE_PENDING;
    }
}
