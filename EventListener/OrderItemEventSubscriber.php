<?php

namespace Ekyna\Bundle\OrderBundle\EventListener;

use Ekyna\Bundle\AdminBundle\Event\ResourceMessage;
use Ekyna\Bundle\OrderBundle\Event\OrderItemEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OrderItemEventSubscriber
 * @package Ekyna\Bundle\OrderBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Pre item add event handler.
     *
     * @param OrderItemEvent $event
     */
    public function onPreItemAdd(OrderItemEvent $event)
    {
        $order = $event->getOrder();
        if ($order->getLocked()) {
            $event->addMessage(new ResourceMessage('ekyna_order.event.locked', ResourceMessage::TYPE_ERROR));
        }
    }

    /**
     * Item add event handler.
     *
     * @param OrderItemEvent $event
     */
    public function onItemAdd(OrderItemEvent $event)
    {
        $order = $event->getOrder();
        $item  = $event->getItem();

        $order->addItem($item);
    }

    /**
     * Post item add event handler.
     *
     * @param OrderItemEvent $event
     */
    public function onPostItemAdd(OrderItemEvent $event)
    {
        $this->dispatcher->dispatch(OrderEvents::CONTENT_CHANGE, $event);
    }

    /**
     * Pre item remove event handler.
     *
     * @param OrderItemEvent $event
     */
    public function onPreItemRemove(OrderItemEvent $event)
    {
        $order = $event->getOrder();
        if ($order->getLocked()) {
            $event->addMessage(new ResourceMessage('ekyna_order.event.locked', ResourceMessage::TYPE_ERROR));
        }
    }

    /**
     * Item remove evet handler.
     *
     * @param OrderItemEvent $event
     */
    public function onItemRemove(OrderItemEvent $event)
    {
        $order = $event->getOrder();
        $item  = $event->getItem();

        $order->removeItem($item);
    }

    /**
     * Post item remove event handler.
     *
     * @param OrderItemEvent $event
     */
    public function onPostItemRemove(OrderItemEvent $event)
    {
        $this->dispatcher->dispatch(OrderEvents::CONTENT_CHANGE, $event);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
    	return array(
    	    OrderEvents::ITEM_ADD => array(
    	        array('onPreItemAdd',      512),
        	    array('onItemAdd',           0),
        	    array('onPostItemAdd',    -512),
    	    ),
    	    OrderEvents::ITEM_REMOVE => array(
    	        array('onPreItemRemove',   512),
    	        array('onItemRemove',        0),
    	        array('onPostItemRemove', -512),
    	    ),
    	);
    }
}
