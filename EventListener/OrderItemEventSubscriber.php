<?php

namespace Ekyna\Bundle\OrderBundle\EventListener;

use Ekyna\Bundle\OrderBundle\Event\OrderItemEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvents;
use Ekyna\Bundle\OrderBundle\Exception\LockedOrderException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * OrderItemEventSubscriber.
 *
 * @author Etienne Dauvergne <contact@ekyna.com>
 */
class OrderItemEventSubscriber implements EventSubscriberInterface
{
    /**
     * Pre item add event handler.
     *
     * @param OrderItemEvent $event
     */
    public function onPreItemAdd(OrderItemEvent $event)
    {
        $order = $event->getOrder();
        if ($order->getLocked()) {
            $event->stopPropagation();
            throw new LockedOrderException();
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
        $event->getDispatcher()->dispatch(OrderEvents::CONTENT_CHANGE, $event);
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
            $event->stopPropagation();
            throw new LockedOrderException();
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
        $event->getDispatcher()->dispatch(OrderEvents::CONTENT_CHANGE, $event);
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
