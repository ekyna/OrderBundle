<?php

namespace Ekyna\Bundle\OrderBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ekyna\Bundle\OrderBundle\Model\UpdaterInterface;
use Ekyna\Component\Sale\Order\OrderEvents;
use Ekyna\Component\Sale\Order\OrderEvent;

/**
 * OrderSubscriber
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Ekyna\Bundle\OrderBundle\Model\UpdaterInterface
     */
    private $updater;

    /**
     * Constructor.
     * 
     * @param \Ekyna\Bundle\OrderBundle\Model\UpdaterInterface $updater
     */
    public function __construct(UpdaterInterface $updater)
    {
        $this->updater = $updater;
    }

    /**
     * Order updated event handler
     * 
     * @param OrderEvent $event
     */
    public function onOrderUpdated(OrderEvent $event)
    {
        $order = $event->getOrder();
        $this->updater->update($order);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
    	return array(
    		OrderEvents::UPDATED => array('onOrderUpdated', 0),
    	);
    }
}
