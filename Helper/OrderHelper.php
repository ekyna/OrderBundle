<?php

namespace Ekyna\Bundle\OrderBundle\Helper;

use Ekyna\Bundle\OrderBundle\Event\OrderItemEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderItemEvents;
use Ekyna\Component\Sale\Order\OrderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class OrderHelper
 * @package Ekyna\Bundle\OrderBundle\Helper
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderHelper implements OrderHelperInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var ItemHelperInterface
     */
    protected $itemHelper;


    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param ItemHelperInterface      $itemHelper
     */
    public function __construct(EventDispatcherInterface $dispatcher, ItemHelperInterface $itemHelper)
    {
        $this->dispatcher = $dispatcher;
        $this->itemHelper = $itemHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSubject(OrderInterface $order, $subject)
    {
        $item = $this->itemHelper->transform($subject);

        foreach ($order->getItems() as $i) {
            if ($i->equals($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addSubject(OrderInterface $order, $subject, $quantity = 1)
    {
        $item = $this->itemHelper->transform($subject);
        $item->setQuantity($quantity);

        $event = new OrderItemEvent($order, $item);
        return $this->dispatcher->dispatch(OrderItemEvents::ADD, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubject(OrderInterface $order, $subject)
    {
        $item = $this->itemHelper->transform($subject);
        $item->setQuantity(1);

        $event = new OrderItemEvent($order, $item);
        return $this->dispatcher->dispatch(OrderItemEvents::REMOVE, $event);
    }
}
