<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Ekyna\Component\Sale\Order\OrderItemInterface;
use Ekyna\Component\Sale\Order\OrderItemOptionInterface;
use Ekyna\Component\Sale\Product\OptionInterface;

/**
 * OrderItemOption.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemOption implements OrderItemOptionInterface
{
    use \Ekyna\Component\Sale\PriceableTrait;
    use \Ekyna\Component\Sale\ReferenceableTrait;
    use \Ekyna\Component\Sale\WeighableTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Ekyna\Bundle\OrderBundle\Entity\OrderItem
     */
    protected $orderItem;

    /**
     * @var \Ekyna\Bundle\ProductBundle\Entity\Option
     */
    protected $option;


    /**
     * Returns the identifier.
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the order item.
     *
     * @param \Ekyna\Component\Sale\Order\OrderItemInterface $orderItem
     * 
     * @return OrderItemOption
     */
    public function setOrderItem(OrderItemInterface $orderItem = null)
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * Sets the option.
     *
     * @param \Ekyna\Component\Sale\Product\OptionInterface $option
     * 
     * @return OrderItemOption
     */
    public function setOption(OptionInterface $option = null)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption()
    {
        return $this->option;
    }
}
