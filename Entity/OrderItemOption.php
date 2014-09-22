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
    use \Ekyna\Component\Sale\WeightableTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Ekyna\Bundle\OrderBundle\Entity\OrderItem
     */
    protected $orderItem;

    /**
     * @var \Ekyna\Component\Sale\Product\OptionInterface
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
