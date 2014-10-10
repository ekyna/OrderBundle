<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepository;
use Ekyna\Component\Sale\Order\OrderTypes;

/**
 * Class OrderRepository
 * @package Ekyna\Bundle\OrderBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderRepository extends ResourceRepository
{
    public function createNew($type = OrderTypes::TYPE_ORDER)
    {
        $order = parent::createNew();
        $order->setType($type);

        return $order;
    }
}
