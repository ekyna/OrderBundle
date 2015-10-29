<?php

namespace Ekyna\Bundle\OrderBundle\Helper;

use Ekyna\Bundle\OrderBundle\Event\OrderItemEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderItemEvents;
use Ekyna\Bundle\OrderBundle\Exception\LogicException;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
        return null !== $this->findItemBySubject($order, $subject);
    }

    /**
     * {@inheritdoc}
     */
    public function addSubject(OrderInterface $order, $subject, $quantity = 1)
    {
        $transformed = $this->itemHelper->transform($subject);
        $transformed->setQuantity($quantity);

        $event = new OrderItemEvent($order, $transformed);
        return $this->dispatcher->dispatch(OrderItemEvents::ADD, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function syncSubject(OrderInterface $order, $subject)
    {
        $transformed = $this->itemHelper->transform($subject);

        if (null === $item = $this->findTransformedItem($order, $transformed)) {
            throw new LogicException('Can\'t sync : subject does not match any order item.');
        }

        $this->synchronizeItems($transformed, $item);

        $event = new OrderItemEvent($order, $item);
        return $this->dispatcher->dispatch(OrderItemEvents::SYNC, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubject(OrderInterface $order, $subject)
    {
        if (null === $item = $this->findItemBySubject($order, $subject)) {
            throw new LogicException('Can\'t remove : subject does not match any order item.');
        }

        $event = new OrderItemEvent($order, $item);
        return $this->dispatcher->dispatch(OrderItemEvents::REMOVE, $event);
    }

    /**
     * Finds the order item by subject.
     *
     * @param OrderInterface $order
     * @param object $subject
     * @return OrderItemInterface|null
     */
    private function findItemBySubject(OrderInterface $order, $subject)
    {
        return $this->findTransformedItem($order, $this->itemHelper->transform($subject));
    }

    /**
     * Finds the transformed item in the order.
     *
     * @param OrderInterface $order
     * @param OrderItemInterface $transformed
     * @return OrderItemInterface|null
     */
    private function findTransformedItem(OrderInterface $order, OrderItemInterface $transformed)
    {
        foreach ($order->getItems() as $item) {
            if ($item->equals($transformed)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Synchronizes the items.
     *
     * @param OrderItemInterface $source
     * @param OrderItemInterface $target
     */
    private function synchronizeItems(OrderItemInterface $source, OrderItemInterface $target)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach (array('designation', 'reference', 'price', 'weight', 'tax') as $property) {
            $value = $accessor->getValue($source, $property);
            if ($accessor->getValue($target, $property) != $value) {
                $accessor->setValue($target, $property, $value);
            }
        }
    }
}
