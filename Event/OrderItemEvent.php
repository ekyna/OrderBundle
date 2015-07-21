<?php

namespace Ekyna\Bundle\OrderBundle\Event;

use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;

/**
 * Class OrderItemEvent
 * @package Ekyna\Bundle\OrderBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemEvent extends OrderEvent
{
    /**
     * @var OrderItemInterface
     */
    protected $item;

    /**
     * @var object
     */
    protected $subject;


    /**
     * Constructor.
     * 
     * @param OrderInterface $order
     * @param OrderItemInterface $item
     */
    public function __construct(OrderInterface $order, OrderItemInterface $item = null)
    {
        parent::__construct($order);

        if ($item) {
            $this->setItem($item);
        }
    }

    /**
     * Sets the item.
     *
     * @param OrderItemInterface $item
     * @return OrderItemEvent
     */
    public function setItem(OrderItemInterface $item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Returns whether the item is set or not.
     *
     * @return bool
     */
    public function hasItem()
    {
        return null !== $this->item;
    }

    /**
     * Returns the item.
     *
     * @return OrderItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Sets the subject.
     *
     * @param object $subject
     * @return OrderItemEvent
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Returns whether the subject is set or not.
     *
     * @return bool
     */
    public function hasSubject()
    {
        return null !== $this->subject;
    }

    /**
     * Returns the subject.
     *
     * @return object
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
