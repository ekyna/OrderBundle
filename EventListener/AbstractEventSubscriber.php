<?php

namespace Ekyna\Bundle\OrderBundle\EventListener;

use Ekyna\Bundle\AdminBundle\Event\ResourceMessage;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AbstractEventSubscriber
 * @package Ekyna\Bundle\OrderBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractEventSubscriber implements EventSubscriberInterface
{
    /**
     * Returns whether the order is locked or not
     * and stop the event propagation if it's the case.
     *
     * @param OrderEvent $event
     * @return bool
     */
    protected function isOrderLocked(OrderEvent $event)
    {
        if ($event->getOrder()->getLocked()) {
            $event->addMessage(new ResourceMessage('ekyna_order.event.locked', ResourceMessage::TYPE_ERROR));
            return true;
        }
        return false;
    }
}
