<?php

namespace Ekyna\Bundle\OrderBundle\EventListener;

use Ekyna\Bundle\ShipmentBundle\Event\ShipmentEvent;
use Ekyna\Bundle\ShipmentBundle\Event\ShipmentEvents;

/**
 * Class ShipmentEventSubscriber
 * @package Ekyna\Bundle\OrderBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ShipmentEventSubscriber extends AbstractEventSubscriber
{
    /**
     * Shipment state change event handler.
     *
     * @param ShipmentEvent $event
     */
    public function onShipmentStateChange(ShipmentEvent $event)
    {
        /* TODO $shipment = $event->getShipment();
        if ($shipment instanceOf OrderShipmentInterface) {
            $order = $shipment->getOrder();
            $this->resolveOrderStates($order, $event);
        }*/
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ShipmentEvents::STATE_CHANGE => ['onShipmentStateChange', 0],
        ];
    }
}
