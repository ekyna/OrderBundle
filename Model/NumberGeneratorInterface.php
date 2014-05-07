<?php

namespace Ekyna\Bundle\OrderBundle\Model;

use Ekyna\Component\Sale\Order\OrderInterface;

interface NumberGeneratorInterface
{
    /**
     * Returns a unique order number.
     * 
     * @param \Ekyna\Component\Sale\Order\OrderInterface $order
     * @param string                                     $type
     * 
     * @return string
     */
    public function generate(OrderInterface $order, $type = OrderInterface::TYPE_ORDER);
}
