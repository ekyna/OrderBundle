<?php

namespace Ekyna\Bundle\OrderBundle\EventListener;

use Ekyna\Bundle\AdminBundle\Operator\ResourceOperatorInterface;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvents;
use Ekyna\Bundle\OrderBundle\Exception\OrderException;
use Ekyna\Bundle\OrderBundle\Service\GeneratorInterface;
use Ekyna\Bundle\OrderBundle\Service\StateResolverInterface;
use Ekyna\Bundle\OrderBundle\Service\CalculatorInterface;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderStates;
use Ekyna\Component\Sale\Order\OrderTypes;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class OrderEventSubscriber
 * @package Ekyna\Bundle\OrderBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderEventSubscriber extends AbstractEventSubscriber
{
    /**
     * @var ResourceOperatorInterface
     */
    private $orderOperator;

    /**
     * @var ResourceOperatorInterface
     */
    private $addressOperator;

    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var StateResolverInterface
     */
    private $stateResolver;

    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @var ValidatorInterface
     */
    private $validator;


    /**
     * Constructor.
     *
     * @param ResourceOperatorInterface $orderOperator
     * @param ResourceOperatorInterface $addressOperator
     * @param CalculatorInterface       $calculator
     * @param StateResolverInterface    $stateResolver
     * @param GeneratorInterface        $generator
     * @param ValidatorInterface        $validator
     */
    public function __construct(
        ResourceOperatorInterface $orderOperator,
        ResourceOperatorInterface $addressOperator,
        CalculatorInterface       $calculator,
        StateResolverInterface    $stateResolver,
        GeneratorInterface        $generator,
        ValidatorInterface        $validator
    )
    {
        $this->orderOperator   = $orderOperator;
        $this->addressOperator = $addressOperator;
        $this->calculator      = $calculator;
        $this->stateResolver   = $stateResolver;
        $this->generator       = $generator;
        $this->validator       = $validator;
    }

    /**
     * Pre content change event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreContentChange(OrderEvent $event)
    {
        $this->isOrderLocked($event);
    }

    /**
     * Content change event handler.
     *
     * @param OrderEvent $event
     */
    public function onContentChange(OrderEvent $event)
    {
        $order = $event->getOrder();

        $this->calculator->updateTotals($order);
        $this->stateResolver->resolve($order, $event);
    }

    /**
     * Post content change event handler.
     *
     * @param OrderEvent $event
     */
    public function onPostContentChange(OrderEvent $event)
    {
        $this->orderOperator->update($event);
    }

    /**
     * State change event handler.
     *
     * @param OrderEvent $event
     * @throws OrderException
     */
    public function onStateChange(OrderEvent $event)
    {
        $order = $event->getOrder();

        if (in_array($order->getState(), array(OrderStates::STATE_ACCEPTED, OrderStates::STATE_COMPLETED))
            && $order->getType() != OrderTypes::TYPE_ORDER
        ) {
            // Set type and created at
            $order
                ->setType(OrderTypes::TYPE_ORDER)
                ->setCreatedAt(new \DateTime())
            ;

            // Generate number
            if (null === $order->getNumber()) {
                $this->generator->generateNumber($order);
            }

            // Generate key
            if (null === $order->getKey()) {
                $this->generator->generateKey($order);
            }

            // Clone invoice address if needed
            $invoiceAddress = $order->getInvoiceAddress();
            if (null !== $invoiceAddress->getUser()) {
                $invoiceAddress = clone $invoiceAddress;
                $invoiceAddress->setUser(null);
                $order->setInvoiceAddress($invoiceAddress);
            }

            // Clone delivery address if needed
            if (!$order->getSameAddress()) {
                $deliveryAddress = $order->getDeliveryAddress();
                if (null !== $deliveryAddress->getUser()) {
                    $deliveryAddress = clone $deliveryAddress;
                    $deliveryAddress->setUser(null);
                    $order->setDeliveryAddress($deliveryAddress);
                }
            }

            $errorList = $this->validator->validate($order, array('Default', 'Order'));
            if ($errorList->count() > 0) {
                throw new OrderException('Invalid order.');
            }
        }

        // Set completed at
        if ($order->getState() === OrderStates::STATE_COMPLETED && null === $order->getCompletedAt()) {
            $order->setCompletedAt(new \DateTime());
        }
    }

    /**
     * Post state change event handler.
     *
     * @param OrderEvent $event
     */
    public function onPostStateChange(OrderEvent $event)
    {
        $this->orderOperator->update($event);
    }

    /**
     * Pre create event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreCreate(OrderEvent $event)
    {
        if ($this->isOrderLocked($event)) {
            return;
        }

        $this->handleDeliveryAddress($event->getOrder());
    }

    /**
     * Pre update event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreUpdate(OrderEvent $event)
    {
        if ($this->isOrderLocked($event)) {
            return;
        }

        $this->handleDeliveryAddress($event->getOrder());
    }

    /**
     * Pre delete event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreDelete(OrderEvent $event)
    {
        $this->isOrderLocked($event);
    }

    /**
     * Removes the delivery address if not used.
     *
     * @param OrderInterface $order
     */
    private function handleDeliveryAddress(OrderInterface $order)
    {
        $deliveryAddress = $order->getDeliveryAddress();
        if (null !== $deliveryAddress && $order->getSameAddress()) {
            $order->setDeliveryAddress(null);
            if (null === $deliveryAddress->getUser()) {
                $this->addressOperator->delete($deliveryAddress);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            OrderEvents::CONTENT_CHANGE => array(
                array('onPreContentChange', 512),
                array('onContentChange', 0),
                array('onPostContentChange', -512),
            ),
            OrderEvents::STATE_CHANGE => array(
                array('onStateChange', 0),
                array('onPostStateChange', -512),
            ),
            OrderEvents::PRE_CREATE => array('onPreCreate', 0),
            OrderEvents::PRE_UPDATE => array('onPreUpdate', 0),
            OrderEvents::PRE_DELETE => array('onPreDelete', 0),
        );
    }
}
