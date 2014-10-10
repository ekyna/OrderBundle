<?php

namespace Ekyna\Bundle\OrderBundle\Model;

use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderTypes;

/**
 * Interface NumberGeneratorInterface
 * @package Ekyna\Bundle\OrderBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface NumberGeneratorInterface
{
    /**
     * Returns a unique order number.
     * 
     * @param OrderInterface $order
     * @param string         $type
     * 
     * @return string
     */
    public function generate(OrderInterface $order, $type = OrderTypes::TYPE_ORDER);
}
