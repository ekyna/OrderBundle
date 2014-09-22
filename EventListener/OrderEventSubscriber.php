<?php

namespace Ekyna\Bundle\OrderBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Ekyna\Bundle\AdminBundle\Event\ResourceMessage;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvents;
use Ekyna\Bundle\OrderBundle\Model\StateResolverInterface;
use Ekyna\Bundle\OrderBundle\Model\UpdaterInterface;
use Ekyna\Bundle\OrderBundle\Model\NumberGeneratorInterface;
use Ekyna\Bundle\PaymentBundle\Event\PaymentEvent;
use Ekyna\Bundle\PaymentBundle\Event\PaymentEvents;
use Ekyna\Bundle\ShipmentBundle\Event\ShipmentEvent;
use Ekyna\Bundle\ShipmentBundle\Event\ShipmentEvents;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderPaymentInterface;
use Ekyna\Component\Sale\Order\OrderStates;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OrderEventSubscriber
 * @package Ekyna\Bundle\OrderBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $manager;

    /**
     * @var \Ekyna\Bundle\OrderBundle\Model\UpdaterInterface
     */
    private $updater;

    /**
     * @var \Ekyna\Bundle\OrderBundle\Model\StateResolverInterface
     */
    private $stateResolver;

    /**
     * @var \Ekyna\Bundle\OrderBundle\Model\NumberGeneratorInterface
     */
    private $generator;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param ObjectManager $manager
     * @param EventDispatcherInterface $dispatcher
     * @param UpdaterInterface $updater
     * @param StateResolverInterface $stateResolver
     * @param NumberGeneratorInterface $generator
     */
    public function __construct(
        ObjectManager $manager,
        EventDispatcherInterface $dispatcher,
        UpdaterInterface $updater,
        StateResolverInterface $stateResolver,
        NumberGeneratorInterface $generator
    )
    {
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
        $this->updater = $updater;
        $this->stateResolver = $stateResolver;
        $this->generator = $generator;
    }

    /**
     * Pre content change event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreContentChange(OrderEvent $event)
    {
        if ($event->getOrder()->getLocked()) {
            $event->addMessage(new ResourceMessage('ekyna_order.event.locked', ResourceMessage::TYPE_DANGER));
        }
    }

    /**
     * Content change event handler.
     *
     * @param OrderEvent $event
     */
    public function onContentChange(OrderEvent $event)
    {
        $order = $event->getOrder();
        $this->updater->update($order);
        $this->resolveOrderStates($order, $event);
    }

    /**
     * Post content change event handler.
     *
     * @param OrderEvent $event
     */
    public function onPostContentChange(OrderEvent $event)
    {
        // Dispatch a new event cause his propagation will be stopped.
        $this->dispatcher->dispatch(OrderEvents::UPDATE, new OrderEvent($event->getOrder()));
    }

    /**
     * State change event handler.
     *
     * @param OrderEvent $event
     */
    public function onStateChange(OrderEvent $event)
    {
        $order = $event->getOrder();
        if (in_array($order->getState(), array(OrderStates::STATE_ACCEPTED, OrderStates::STATE_COMPLETED))
            && $order->getType() != OrderInterface::TYPE_ORDER
        ) {
            $order
                ->setType(OrderInterface::TYPE_ORDER)
                ->setCreatedAt(new \DateTime());
            if (null === $order->getNumber()) {
                $order->setNumber($this->generator->generate($order));
            }
        }
    }

    /**
     * Post state change event handler.
     *
     * @param OrderEvent $event
     */
    public function onPostStateChange(OrderEvent $event)
    {
        // Dispatch a new event cause his propagation will be stopped.
        $this->dispatcher->dispatch(OrderEvents::UPDATE, new OrderEvent($event->getOrder()));
    }

    /**
     * Pre update event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreUpdate(OrderEvent $event)
    {
        // TODO: validation
    }

    /**
     * Update event handler.
     *
     * @param OrderEvent $event
     */
    public function onUpdate(OrderEvent $event)
    {
        $this->manager->persist($event->getOrder());
        $this->manager->flush();
        // Prevents from being persisted a second time in ResourceController
        $event->stopPropagation();
    }

    /**
     * Pre delete event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreDelete(OrderEvent $event)
    {
        if ($event->getOrder()->getLocked()) {
            $event->addMessage(new ResourceMessage('ekyna_order.event.locked', ResourceMessage::TYPE_DANGER));
        }
    }

    /**
     * Remove event handler.
     *
     * @param OrderEvent $event
     */
    public function onDelete(OrderEvent $event)
    {
        $this->manager->remove($event->getOrder());
        $this->manager->flush();
        // Prevents being removed a second time in ResourceController
        $event->stopPropagation();
    }

    /**
     * Payment initialize event handler.
     *
     * @param OrderEvent $event
     */
    public function onPaymentInitialize(OrderEvent $event)
    {
        $order = $event->getOrder();
        if ($order->getLocked()) {
            $event->addMessage(new ResourceMessage('ekyna_order.event.locked', ResourceMessage::TYPE_DANGER));
            return;
        }
        $order->setLocked(true);
        // Dispatch a new event cause his propagation will be stopped.
        $this->dispatcher->dispatch(OrderEvents::UPDATE, new OrderEvent($event->getOrder()));
    }

    /**
     * Payment complete event handler.
     *
     * @param OrderEvent $event
     */
    public function onPaymentComplete(OrderEvent $event)
    {
        $order = $event->getOrder();
        if ($order->getLocked()) {
            $order->setLocked(false);
            // Dispatch a new event cause his propagation will be stopped.
            $this->dispatcher->dispatch(OrderEvents::UPDATE, new OrderEvent($event->getOrder()));
        }
    }

    /**
     * Payment state change event handler.
     *
     * @param PaymentEvent $event
     */
    public function onPaymentStateChange(PaymentEvent $event)
    {
        $payment = $event->getPayment();
        if ($payment instanceOf OrderPaymentInterface) {
            $order = $payment->getOrder();
            $this->resolveOrderStates($order, $event);
        }
    }

    /**
     * Shipment state change event handler.
     *
     * @param ShipmentEvent $event
     */
    public function onShipmentPostStateChange(ShipmentEvent $event)
    {
        /*$shipment = $event->getShipment();
        if ($shipment instanceOf OrderShipmentInterface) {
            $order = $shipment->getOrder();
            $this->resolveOrderStates($order, $event);
        }*/
    }

    /**
     * Resolves order states.
     *
     * @param OrderInterface $order
     * @param Event $event
     */
    private function resolveOrderStates(OrderInterface $order, Event $event)
    {
        $oldState = $order->getState();
        $this->stateResolver->resolve($order);
        $newState = $order->getState();

        if ($newState != $oldState) {
            $event->getDispatcher()->dispatch(
                OrderEvents::STATE_CHANGE,
                $event instanceof OrderEvent ? $event : new OrderEvent($order)
            );
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
            OrderEvents::UPDATE => array(
                array('onPreUpdate', 512),
                array('onUpdate', 0),
            ),
            OrderEvents::DELETE => array(
                array('onPreDelete', 512),
                array('onDelete', 0),
            ),
            OrderEvents::PAYMENT_INITIALIZE => array('onPaymentInitialize', 0),
            OrderEvents::PAYMENT_COMPLETE => array('onPaymentComplete', 0),
            PaymentEvents::STATE_CHANGE => array('onPaymentStateChange', 0),
            ShipmentEvents::STATE_CHANGE => array('onShipmentStateChange', 0),
        );
    }
}
