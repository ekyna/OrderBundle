<?php

namespace Ekyna\Bundle\OrderBundle\Listener;

use Ekyna\Bundle\OrderBundle\Model\StateResolverInterface;
use Ekyna\Bundle\OrderBundle\Model\UpdaterInterface;
use Ekyna\Component\Sale\Order\OrderPaymentInterface;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvents;
use Ekyna\Bundle\PaymentBundle\Event\PaymentEvent;
use Ekyna\Bundle\PaymentBundle\Event\PaymentEvents;
use Ekyna\Bundle\ShipmentBundle\Event\ShipmentEvent;
use Ekyna\Bundle\ShipmentBundle\Event\ShipmentEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ekyna\Component\Sale\Order\OrderStates;
use Ekyna\Component\Sale\Order\OrderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\Event;
use Ekyna\Bundle\OrderBundle\Model\NumberGeneratorInterface;

/**
 * OrderSubscriber
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderSubscriber implements EventSubscriberInterface
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
     * Constructor.
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager               $manager
     * @param \Ekyna\Bundle\OrderBundle\Model\UpdaterInterface         $updater
     * @param \Ekyna\Bundle\OrderBundle\Model\StateResolverInterface   $stateResolver
     * @param \Ekyna\Bundle\OrderBundle\Model\NumberGeneratorInterface $generator
     */
    public function __construct(ObjectManager $manager, UpdaterInterface $updater, StateResolverInterface $stateResolver, NumberGeneratorInterface $generator)
    {
        $this->manager = $manager;
        $this->updater = $updater;
        $this->stateResolver = $stateResolver;
        $this->generator = $generator;
    }

    /**
     * Order pre content change event handler.
     * 
     * @param \Ekyna\Bundle\OrderBundle\Event\OrderEvent $event
     */
    public function onOrderPreContentChange(OrderEvent $event)
    {
    }

    /**
     * Order post content change event handler.
     * 
     * @param \Ekyna\Bundle\OrderBundle\Event\OrderEvent $event
     */
    public function onOrderPostContentChange(OrderEvent $event)
    {
        $order = $event->getOrder();
        $this->updater->update($order);
        $this->manager->persist($order);
        $this->manager->flush();
    }

    /**
     * Order pre payment process event handler.
     * 
     * @param \Ekyna\Bundle\OrderBundle\Event\OrderEvent $event
     */
    public function onOrderPrePaymentProcess(OrderEvent $event)
    {
        $order = $event->getOrder();
        $order->setLocked(true);
        $this->manager->persist($order);
        $this->manager->flush();
    }

    /**
     * Order post payment process event handler.
     * 
     * @param \Ekyna\Bundle\OrderBundle\Event\OrderEvent $event
     */
    public function onOrderPostPaymentProcess(OrderEvent $event)
    {
        $order = $event->getOrder();
        $order->setLocked(false);
        $this->manager->persist($order);
        $this->manager->flush();
    }

    /**
     * Order pre state change event handler.
     * 
     * @param \Ekyna\Bundle\OrderBundle\Event\OrderEvent $event
     */
    public function onOrderPreStateChange(OrderEvent $event)
    {
    }

    /**
     * Order post state change event handler.
     * 
     * @param \Ekyna\Bundle\OrderBundle\Event\OrderEvent $event
     */
    public function onOrderPostStateChange(OrderEvent $event)
    {
        $order = $event->getOrder();
        if(in_array($order->getState(), array(OrderStates::STATE_ACCEPTED, OrderStates::STATE_COMPLETED)) 
            && $order->getType() != OrderInterface::TYPE_ORDER) {
            $order
                ->setType(OrderInterface::TYPE_ORDER)
                ->setCreatedAt(new \Datetime())
            ;
            if(null === $order->getNumber()) {
                $order->setNumber($this->generator->generate($order));
            }

            $this->manager->persist($order);
            $this->manager->flush();
        }
    }

    /**
     * Payment pre state change event handler.
     * 
     * @param \Ekyna\Bundle\PaymentBundle\Event\PaymentEvent $event
     */
    public function onPaymentPreStateChange(PaymentEvent $event)
    {
    }

    /**
     * Payment post state change event handler.
     * 
     * @param \Ekyna\Bundle\PaymentBundle\Event\PaymentEvent $event
     */
    public function onPaymentPostStateChange(PaymentEvent $event)
    {
        $payment = $event->getPayment();
        if ($payment instanceOf OrderPaymentInterface) {
            $order = $payment->getOrder();
            $this->resolveOrderStates($order, $event);
        }
    }

    /**
     * Shipment pre state change event handler.
     * 
     * @param \Ekyna\Bundle\ShipmentBundle\Event\ShipmentEvent $event
     */
    public function onShipmentPreStateChange(ShipmentEvent $event)
    {
    }

    /**
     * Shipment post state change event handler.
     * 
     * @param \Ekyna\Bundle\ShipmentBundle\Event\ShipmentEvent $event
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

        $this->manager->persist($order);
        $this->manager->flush();

        if ($newState != $oldState) {
            $event->getDispatcher()->dispatch(OrderEvents::POST_STATE_CHANGE, new OrderEvent($order));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
    	return array(
    		OrderEvents::PRE_CONTENT_CHANGE   => array('onOrderPreContentChange',   128),
    		OrderEvents::POST_CONTENT_CHANGE  => array('onOrderPostContentChange',  128),

    		OrderEvents::PRE_PAYMENT_PROCESS  => array('onOrderPrePaymentProcess',  128),
    		OrderEvents::POST_PAYMENT_PROCESS => array('onOrderPostPaymentProcess', 128),

    		OrderEvents::PRE_STATE_CHANGE     => array('onOrderPreStateChange',     128),
    		OrderEvents::POST_STATE_CHANGE    => array('onOrderPostStateChange',    128),

    		PaymentEvents::PRE_STATE_CHANGE   => array('onPaymentPreStateChange',   128),
    		PaymentEvents::POST_STATE_CHANGE  => array('onPaymentPostStateChange',  128),

    		ShipmentEvents::PRE_STATE_CHANGE  => array('onShipmentPreStateChange',  128),
    		ShipmentEvents::POST_STATE_CHANGE => array('onShipmentPostStateChange', 128),
    	);
    }
}
